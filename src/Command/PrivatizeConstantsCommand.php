<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Output\OutputPrinter;
use Entropy\Console\Output\ProgressBar;
use Entropy\FileSystem\FileInfo;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Rector\SwissKnife\Contract\ClassConstantFetchInterface;
use Rector\SwissKnife\Finder\PhpFilesFinder;
use Rector\SwissKnife\PhpParser\Finder\ClassConstantFetchFinder;
use Rector\SwissKnife\PhpParser\Finder\ClassConstFinder;
use Rector\SwissKnife\Twig\TwigTemplateConstantExtractor;
use Rector\SwissKnife\ValueObject\ClassConstant;
use Rector\SwissKnife\ValueObject\ClassConstantFetch\CurrentClassConstantFetch;
use Rector\SwissKnife\ValueObject\VisibilityChangeStats;
use Rector\SwissKnife\YAML\YamlConfigConstantExtractor;

final readonly class PrivatizeConstantsCommand implements CommandInterface
{
    public function __construct(
        private OutputPrinter $outputPrinter,
        private ClassConstantFetchFinder $classConstantFetchFinder,
        private ClassConstFinder $classConstFinder,
        private TwigTemplateConstantExtractor $twigTemplateConstantExtractor,
        private YamlConfigConstantExtractor $yamlConfigConstantExtractor
    ) {
    }

    public function getName(): string
    {
        return 'privatize-constants';
    }

    public function getDescription(): string
    {
        return 'Make class constants private if not used outside in PHP, Twig and YAML files';
    }

    /**
     * @param string[] $sources One or more paths to check, include tests directory as well
     * @param string[] $excludedPaths Paths to exclude
     * @param bool $isDebug Debug output
     * @param bool $dryRun Do no change anything, only list constants able to be privatized. If there are constants to privatize, it will exit with code 1. Useful for CI.
     * @return ExitCode::*
     */
    public function run(
        array $sources,
        array $excludedPaths = [],
        bool $isDebug = false,
        bool $dryRun = false
    ): int {
        $phpFileInfos = PhpFilesFinder::find($sources, $excludedPaths);
        if ($phpFileInfos === []) {
            $this->outputPrinter->warning('No PHP files found in provided paths');

            return ExitCode::SUCCESS;
        }

        $this->outputPrinter->title('Finding class const fetches...');

        $progressBar = new ProgressBar();
        $progressBar->start(count($phpFileInfos));

        $phpClassConstantFetches = $this->classConstantFetchFinder->find($phpFileInfos, $progressBar, $isDebug);

        // find usage in twig files
        $twigClassConstantFetches = $this->twigTemplateConstantExtractor->extractFromDirs($sources);
        $yamlClassConstantFetches = $this->yamlConfigConstantExtractor->extractFromDirs($sources);

        $classConstantFetches = array_merge(
            $phpClassConstantFetches,
            $twigClassConstantFetches,
            $yamlClassConstantFetches
        );

        $this->outputPrinter->newline(2);
        $constantFetchCount = count($classConstantFetches);
        $this->outputPrinter->success(sprintf(
            'Found %d class constant fetch%s',
            $constantFetchCount,
            $constantFetchCount === 1 ? '' : 'es'
        ));
        $twigFetchCount = count($twigClassConstantFetches);
        $this->outputPrinter->success(
            sprintf('Found %d constant%s in Twig templates', $twigFetchCount, $twigFetchCount === 1 ? '' : 's')
        );
        $yamlFetchCount = count($yamlClassConstantFetches);
        $this->outputPrinter->success(sprintf(
            'Found %d constant%s in YAML configs',
            $yamlFetchCount,
            $yamlFetchCount === 1 ? '' : 's'
        ));

        $this->outputPrinter->newline(2);

        $this->outputPrinter->title('Changing class constant visibility based on use...');

        $visibilityChangeStats = new VisibilityChangeStats();

        // go file by file and deal with public + protected constants
        foreach ($phpFileInfos as $phpFileInfo) {
            $currentVisibilityChangeStats = $this->processFileInfo($phpFileInfo, $classConstantFetches, $dryRun);
            $visibilityChangeStats->merge($currentVisibilityChangeStats);
        }

        if (! $visibilityChangeStats->hasAnyChange()) {
            $this->outputPrinter->warning('No constants were privatized');

            return ExitCode::SUCCESS;
        }

        $this->outputPrinter->newline(2);

        // to make it fail in CI
        if ($dryRun) {
            $privatizableCount = $visibilityChangeStats->getPrivateCount();
            $this->outputPrinter->error(
                sprintf('%d constant%s can be privatized', $privatizableCount, $privatizableCount === 1 ? '' : 's')
            );

            return ExitCode::ERROR;
        }

        $privatizedCount = $visibilityChangeStats->getPrivateCount();
        $this->outputPrinter->success(
            sprintf('Made %d constant%s private', $privatizedCount, $privatizedCount === 1 ? '' : 's')
        );

        return ExitCode::SUCCESS;
    }

    /**
     * @param ClassConstantFetchInterface[] $classConstantFetches
     */
    private function processFileInfo(
        FileInfo $phpFileInfo,
        array $classConstantFetches,
        bool $dryRun
    ): VisibilityChangeStats {
        $visibilityChangeStats = new VisibilityChangeStats();

        $classConstants = $this->classConstFinder->find($phpFileInfo->getRealPath());
        if ($classConstants === []) {
            return $visibilityChangeStats;
        }

        foreach ($classConstants as $classConstant) {
            if ($this->isClassConstantUsedPublicly($classConstantFetches, $classConstant)) {
                // keep it public
                continue;
            }

            $visibilityChangeStats->countPrivate();

            if ($dryRun) {
                $this->outputPrinter->writeln(
                    sprintf('Constant "%s" could be changed to private', $classConstant->getConstantName())
                );
                continue;
            }

            // make private
            $changedFileContents = Strings::replace(
                $phpFileInfo->getContents(),
                '#((private|public|protected)\s+)?const\s+' . $classConstant->getConstantName() . '#',
                'private const ' . $classConstant->getConstantName()
            );
            FileSystem::write($phpFileInfo->getRealPath(), $changedFileContents, null);

            $this->outputPrinter->writeln(
                sprintf('Constant "%s" changed to private', $classConstant->getConstantName())
            );
        }

        return $visibilityChangeStats;
    }

    /**
     * @param ClassConstantFetchInterface[] $classConstantFetches
     */
    private function isClassConstantUsedPublicly(array $classConstantFetches, ClassConstant $classConstant): bool
    {
        foreach ($classConstantFetches as $classConstantFetch) {
            if (! $classConstantFetch->isClassConstantMatch($classConstant)) {
                continue;
            }

            // used only locally, can stay private
            if ($classConstantFetch instanceof CurrentClassConstantFetch) {
                continue;
            }

            // used externally, make public
            return true;
        }

        return false;
    }
}

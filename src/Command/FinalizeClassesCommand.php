<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Output\OutputPrinter;
use Entropy\Console\Output\ProgressBar;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Rector\SwissKnife\Analyzer\NeedsFinalizeAnalyzer;
use Rector\SwissKnife\EntityClassResolver;
use Rector\SwissKnife\FileSystem\PathHelper;
use Rector\SwissKnife\Finder\PhpFilesFinder;
use Rector\SwissKnife\MockedClassResolver;
use Rector\SwissKnife\ParentClassResolver;
use Rector\SwissKnife\PhpParser\CachedPhpParser;

final readonly class FinalizeClassesCommand implements CommandInterface
{
    /**
     * @see https://regex101.com/r/Q5Nfbo/1
     */
    private const string NEWLINE_CLASS_START_REGEX = '#^(readonly )?class\s#m';

    public function __construct(
        private OutputPrinter $outputPrinter,
        private ParentClassResolver $parentClassResolver,
        private EntityClassResolver $entityClassResolver,
        private CachedPhpParser $cachedPhpParser,
        private MockedClassResolver $mockedClassResolver,
    ) {
    }

    /**
     * @param string[] $paths Directories to finalize
     * @param bool $dryRun Do no change anything, only list classes about to be finalized. If there are classes to finalize, it will exit with code 1. Useful for CI.
     * @param bool $skipMocked Skip mocked classes as well (use only if unable to run bypass-finals package)
     * @param string[] $skipFiles Skip file or files by path
     * @param bool $noProgress Do not show progress bar, only results
     */
    public function run(
        array $paths,
        bool $dryRun = false,
        bool $skipMocked = false,
        array $skipFiles = [],
        bool $noProgress = false
    ): int {
        $this->outputPrinter->title('1. Detecting parent and entity classes');

        $phpFileInfos = PhpFilesFinder::find($paths, $skipFiles);

        $progressBar = null;
        if (! $noProgress) {
            // double to count for both parent and entity resolver
            $stepRatio = $skipMocked ? 3 : 2;

            $progressBar = new ProgressBar();
            $progressBar->start($stepRatio * count($phpFileInfos));
        }

        $progressClosure = function () use ($noProgress, $progressBar): void {
            if ($noProgress || ! $progressBar instanceof ProgressBar) {
                return;
            }

            $progressBar->advance();
        };

        $parentClassNames = $this->parentClassResolver->resolve($phpFileInfos, $progressClosure);
        $entityClassNames = $this->entityClassResolver->resolve($paths, $progressClosure);

        $mockedClassNames = $skipMocked ? $this->mockedClassResolver->resolve($paths, $progressClosure) : [];

        if ($progressBar instanceof ProgressBar) {
            $progressBar->finish();
        }

        $this->outputPrinter->writeln(sprintf(
            'Found %d parent and %d entity classes',
            count($parentClassNames),
            count($entityClassNames)
        ));

        if ($skipMocked) {
            $this->outputPrinter->writeln(sprintf('Also %d mocked classes', count($mockedClassNames)));
        }

        $this->outputPrinter->newline(1);

        $this->outputPrinter->title('2. Finalizing safe classes');

        $excludedClasses = array_merge($parentClassNames, $entityClassNames, $mockedClassNames);
        $needsFinalizeAnalyzer = new NeedsFinalizeAnalyzer($excludedClasses, $this->cachedPhpParser);

        $finalizedFilePaths = [];

        foreach ($phpFileInfos as $phpFileInfo) {
            // should be file be finalize, is not and is not excluded?
            if (! $needsFinalizeAnalyzer->isNeeded($phpFileInfo->getRealPath())) {
                continue;
            }

            $finalizedContents = Strings::replace(
                $phpFileInfo->getContents(),
                self::NEWLINE_CLASS_START_REGEX,
                'final $1class '
            );

            $finalizedFilePaths[] = PathHelper::relativeToCwd($phpFileInfo->getRealPath());

            if ($dryRun === false) {
                FileSystem::write($phpFileInfo->getRealPath(), $finalizedContents, null);
            }
        }

        if ($finalizedFilePaths === []) {
            $this->outputPrinter->success('Nothing to finalize');
            return ExitCode::SUCCESS;
        }

        $this->outputPrinter->listing($finalizedFilePaths);

        $countFinalizedClasses = count($finalizedFilePaths);
        $pluralClassText = $countFinalizedClasses === 1 ? 'class' : 'classes';

        // to make it fail in CI
        if ($dryRun) {
            $this->outputPrinter->error(sprintf(
                '%d %s can be finalized',
                $countFinalizedClasses,
                $pluralClassText,
            ));

            return ExitCode::ERROR;
        }

        $this->outputPrinter->success(sprintf('%d %s finalized', $countFinalizedClasses, $pluralClassText));

        return ExitCode::SUCCESS;
    }

    public function getName(): string
    {
        return 'finalize-classes';
    }

    public function getDescription(): string
    {
        return 'Finalize classes without children';
    }
}

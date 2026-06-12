<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use SwissKnife202606\Entropy\Console\Contract\CommandInterface;
use SwissKnife202606\Entropy\Console\Enum\ExitCode;
use SwissKnife202606\Entropy\Console\Output\OutputColorizer;
use SwissKnife202606\Entropy\Console\Output\OutputPrinter;
use SwissKnife202606\Entropy\Console\Output\ProgressBar;
use SwissKnife202606\Nette\Utils\FileSystem;
use SwissKnife202606\Nette\Utils\Strings;
use Rector\SwissKnife\Contract\ClassConstantFetchInterface;
use Rector\SwissKnife\Finder\PhpFilesFinder;
use Rector\SwissKnife\PhpParser\Finder\ClassConstantFetchFinder;
use Rector\SwissKnife\PhpParser\Finder\ClassConstFinder;
use Rector\SwissKnife\Twig\TwigTemplateConstantExtractor;
use Rector\SwissKnife\ValueObject\ClassConstant;
use Rector\SwissKnife\ValueObject\ClassConstantFetch\CurrentClassConstantFetch;
use Rector\SwissKnife\ValueObject\VisibilityChangeStats;
use Rector\SwissKnife\YAML\YamlConfigConstantExtractor;
use SwissKnife202606\Symfony\Component\Finder\SplFileInfo;
final class PrivatizeConstantsCommand implements CommandInterface
{
    /**
     * @readonly
     * @var \Entropy\Console\Output\OutputPrinter
     */
    private $outputPrinter;
    /**
     * @readonly
     * @var \Entropy\Console\Output\OutputColorizer
     */
    private $outputColorizer;
    /**
     * @readonly
     * @var \Rector\SwissKnife\PhpParser\Finder\ClassConstantFetchFinder
     */
    private $classConstantFetchFinder;
    /**
     * @readonly
     * @var \Rector\SwissKnife\PhpParser\Finder\ClassConstFinder
     */
    private $classConstFinder;
    /**
     * @readonly
     * @var \Rector\SwissKnife\Twig\TwigTemplateConstantExtractor
     */
    private $twigTemplateConstantExtractor;
    /**
     * @readonly
     * @var \Rector\SwissKnife\YAML\YamlConfigConstantExtractor
     */
    private $yamlConfigConstantExtractor;
    public function __construct(OutputPrinter $outputPrinter, OutputColorizer $outputColorizer, ClassConstantFetchFinder $classConstantFetchFinder, ClassConstFinder $classConstFinder, TwigTemplateConstantExtractor $twigTemplateConstantExtractor, YamlConfigConstantExtractor $yamlConfigConstantExtractor)
    {
        $this->outputPrinter = $outputPrinter;
        $this->outputColorizer = $outputColorizer;
        $this->classConstantFetchFinder = $classConstantFetchFinder;
        $this->classConstFinder = $classConstFinder;
        $this->twigTemplateConstantExtractor = $twigTemplateConstantExtractor;
        $this->yamlConfigConstantExtractor = $yamlConfigConstantExtractor;
    }
    public function getName() : string
    {
        return 'privatize-constants';
    }
    public function getDescription() : string
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
    public function run(array $sources, array $excludedPaths = [], bool $isDebug = \false, bool $dryRun = \false) : int
    {
        $phpFileInfos = PhpFilesFinder::find($sources, $excludedPaths);
        if ($phpFileInfos === []) {
            $this->outputPrinter->warning('No PHP files found in provided paths');
            return ExitCode::SUCCESS;
        }
        $this->outputPrinter->title('Finding class const fetches...');
        $progressBar = new ProgressBar($this->outputColorizer);
        $progressBar->start(\count($phpFileInfos));
        $phpClassConstantFetches = $this->classConstantFetchFinder->find($phpFileInfos, $progressBar, $isDebug);
        // find usage in twig files
        $twigClassConstantFetches = $this->twigTemplateConstantExtractor->extractFromDirs($sources);
        $yamlClassConstantFetches = $this->yamlConfigConstantExtractor->extractFromDirs($sources);
        $classConstantFetches = \array_merge($phpClassConstantFetches, $twigClassConstantFetches, $yamlClassConstantFetches);
        $this->outputPrinter->newline(2);
        $this->outputPrinter->success(\sprintf('Found %d class constant fetches', \count($classConstantFetches)));
        $this->outputPrinter->success(\sprintf('Found %d constants in Twig templates', \count($twigClassConstantFetches)));
        $this->outputPrinter->success(\sprintf('Found %d constants in YAML configs', \count($yamlClassConstantFetches)));
        $this->outputPrinter->newline(2);
        $this->outputPrinter->title('Changing class constant visibility based on use...');
        $visibilityChangeStats = new VisibilityChangeStats();
        // go file by file and deal with public + protected constants
        foreach ($phpFileInfos as $phpFileInfo) {
            $currentVisibilityChangeStats = $this->processFileInfo($phpFileInfo, $classConstantFetches, $dryRun);
            $visibilityChangeStats->merge($currentVisibilityChangeStats);
        }
        if (!$visibilityChangeStats->hasAnyChange()) {
            $this->outputPrinter->warning('No constants were privatized');
            return ExitCode::SUCCESS;
        }
        $this->outputPrinter->newline(2);
        // to make it fail in CI
        if ($dryRun) {
            $this->outputPrinter->error(\sprintf('%d constants can be privatized', $visibilityChangeStats->getPrivateCount()));
            return ExitCode::ERROR;
        }
        $this->outputPrinter->success(\sprintf('Totally %d constants were made private', $visibilityChangeStats->getPrivateCount()));
        return ExitCode::SUCCESS;
    }
    /**
     * @param ClassConstantFetchInterface[] $classConstantFetches
     */
    private function processFileInfo(SplFileInfo $phpFileInfo, array $classConstantFetches, bool $dryRun) : VisibilityChangeStats
    {
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
                $this->outputPrinter->writeln(\sprintf('Constant "%s" could be changed to private', $classConstant->getConstantName()));
                continue;
            }
            // make private
            $changedFileContents = Strings::replace($phpFileInfo->getContents(), '#((private|public|protected)\\s+)?const\\s+' . $classConstant->getConstantName() . '#', 'private const ' . $classConstant->getConstantName());
            FileSystem::write($phpFileInfo->getRealPath(), $changedFileContents, null);
            $this->outputPrinter->writeln(\sprintf('Constant "%s" changed to private', $classConstant->getConstantName()));
        }
        return $visibilityChangeStats;
    }
    /**
     * @param ClassConstantFetchInterface[] $classConstantFetches
     */
    private function isClassConstantUsedPublicly(array $classConstantFetches, ClassConstant $classConstant) : bool
    {
        foreach ($classConstantFetches as $classConstantFetch) {
            if (!$classConstantFetch->isClassConstantMatch($classConstant)) {
                continue;
            }
            // used only locally, can stay private
            if ($classConstantFetch instanceof CurrentClassConstantFetch) {
                continue;
            }
            // used externally, make public
            return \true;
        }
        return \false;
    }
}

<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use SwissKnife202409\Nette\Utils\FileSystem;
use SwissKnife202409\Nette\Utils\Strings;
use Rector\SwissKnife\Analyzer\NeedsFinalizeAnalyzer;
use Rector\SwissKnife\EntityClassResolver;
use Rector\SwissKnife\FileSystem\PathHelper;
use Rector\SwissKnife\Finder\PhpFilesFinder;
use Rector\SwissKnife\MockedClassResolver;
use Rector\SwissKnife\ParentClassResolver;
use Rector\SwissKnife\PhpParser\CachedPhpParser;
use SwissKnife202409\Symfony\Component\Console\Command\Command;
use SwissKnife202409\Symfony\Component\Console\Input\InputArgument;
use SwissKnife202409\Symfony\Component\Console\Input\InputInterface;
use SwissKnife202409\Symfony\Component\Console\Input\InputOption;
use SwissKnife202409\Symfony\Component\Console\Output\OutputInterface;
use SwissKnife202409\Symfony\Component\Console\Style\SymfonyStyle;
final class FinalizeClassesCommand extends Command
{
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @readonly
     * @var \Rector\SwissKnife\ParentClassResolver
     */
    private $parentClassResolver;
    /**
     * @readonly
     * @var \Rector\SwissKnife\EntityClassResolver
     */
    private $entityClassResolver;
    /**
     * @readonly
     * @var \Rector\SwissKnife\PhpParser\CachedPhpParser
     */
    private $cachedPhpParser;
    /**
     * @readonly
     * @var \Rector\SwissKnife\MockedClassResolver
     */
    private $mockedClassResolver;
    /**
     * @see https://regex101.com/r/Q5Nfbo/1
     */
    private const NEWLINE_CLASS_START_REGEX = '#^(readonly )?class\\s#m';
    public function __construct(SymfonyStyle $symfonyStyle, ParentClassResolver $parentClassResolver, EntityClassResolver $entityClassResolver, CachedPhpParser $cachedPhpParser, MockedClassResolver $mockedClassResolver)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->parentClassResolver = $parentClassResolver;
        $this->entityClassResolver = $entityClassResolver;
        $this->cachedPhpParser = $cachedPhpParser;
        $this->mockedClassResolver = $mockedClassResolver;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('finalize-classes');
        $this->setAliases(['finalise', 'finalise-classes']);
        $this->setDescription('Finalize classes without children');
        $this->addArgument('paths', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Directories to finalize');
        $this->addOption('skip-mocked', null, InputOption::VALUE_NONE, 'Skip mocked classes as well (use only if unable to run bypass-finals package)');
        $this->addOption('skip-file', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Skip file or files by path');
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do no change anything, only list classes about to be finalized');
    }
    /**
     * @return self::FAILURE|self::SUCCESS
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $paths = (array) $input->getArgument('paths');
        $isDryRun = (bool) $input->getOption('dry-run');
        $areMockedSkipped = (bool) $input->getOption('skip-mocked');
        $this->symfonyStyle->title('1. Detecting parent and entity classes');
        $skippedFiles = $input->getOption('skip-file');
        $phpFileInfos = PhpFilesFinder::find($paths, $skippedFiles);
        // double to count for both parent and entity resolver
        $stepRatio = $areMockedSkipped ? 3 : 2;
        $this->symfonyStyle->progressStart($stepRatio * \count($phpFileInfos));
        $progressClosure = function () : void {
            $this->symfonyStyle->progressAdvance();
        };
        $parentClassNames = $this->parentClassResolver->resolve($phpFileInfos, $progressClosure);
        $entityClassNames = $this->entityClassResolver->resolve($paths, $progressClosure);
        $mockedClassNames = $areMockedSkipped ? $this->mockedClassResolver->resolve($paths, $progressClosure) : [];
        $this->symfonyStyle->progressFinish();
        $this->symfonyStyle->writeln(\sprintf('Found %d parent and %d entity classes', \count($parentClassNames), \count($entityClassNames)));
        if ($areMockedSkipped) {
            $this->symfonyStyle->writeln(\sprintf('Also %d mocked classes', \count($mockedClassNames)));
        }
        $this->symfonyStyle->newLine(1);
        $this->symfonyStyle->title('2. Finalizing safe classes');
        $excludedClasses = \array_merge($parentClassNames, $entityClassNames, $mockedClassNames);
        $needsFinalizeAnalyzer = new NeedsFinalizeAnalyzer($excludedClasses, $this->cachedPhpParser);
        $finalizedFilePaths = [];
        foreach ($phpFileInfos as $phpFileInfo) {
            // should be file be finalize, is not and is not excluded?
            if (!$needsFinalizeAnalyzer->isNeeded($phpFileInfo->getRealPath())) {
                continue;
            }
            $finalizedContents = Strings::replace($phpFileInfo->getContents(), self::NEWLINE_CLASS_START_REGEX, 'final $1class ');
            $finalizedFilePaths[] = PathHelper::relativeToCwd($phpFileInfo->getRealPath());
            if ($isDryRun === \false) {
                FileSystem::write($phpFileInfo->getRealPath(), $finalizedContents);
            }
        }
        if ($finalizedFilePaths === []) {
            $this->symfonyStyle->success('Nothing to finalize');
            return self::SUCCESS;
        }
        $this->symfonyStyle->listing($finalizedFilePaths);
        $this->symfonyStyle->success(\sprintf('%d classes %s finalized', \count($finalizedFilePaths), $isDryRun ? 'would be' : 'were'));
        // to make it fail in CI
        if ($isDryRun) {
            return self::FAILURE;
        }
        return Command::SUCCESS;
    }
}

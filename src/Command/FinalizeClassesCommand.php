<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Rector\SwissKnife\Analyzer\NeedsFinalizeAnalyzer;
use Rector\SwissKnife\EntityClassResolver;
use Rector\SwissKnife\FileSystem\PathHelper;
use Rector\SwissKnife\Finder\PhpFilesFinder;
use Rector\SwissKnife\MockedClassResolver;
use Rector\SwissKnife\ParentClassResolver;
use Rector\SwissKnife\PhpParser\CachedPhpParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class FinalizeClassesCommand extends Command
{
    /**
     * @see https://regex101.com/r/Q5Nfbo/1
     */
    public const NEWLINE_CLASS_START_REGEX = '#^class\s#m';

    public function __construct(
        private readonly SymfonyStyle $symfonyStyle,
        private readonly ParentClassResolver $parentClassResolver,
        private readonly EntityClassResolver $entityClassResolver,
        private readonly CachedPhpParser $cachedPhpParser,
        private readonly MockedClassResolver $mockedClassResolver,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('finalize-classes');

        $this->setDescription('Finalize classes without children');

        $this->addArgument('paths', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Directories to finalize');

        $this->addOption(
            'skip-mocked',
            null,
            InputOption::VALUE_NONE,
            'Skip mocked classes as well (use only if unable to run bypass-finals package)'
        );

        $this->addOption(
            'dry-run',
            null,
            InputOption::VALUE_NONE,
            'Do no change anything, only list classes about to be finalized'
        );
    }

    /**
     * @return self::FAILURE|self::SUCCESS
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $paths = (array) $input->getArgument('paths');
        $isDryRun = (bool) $input->getOption('dry-run');
        $areMockedSkipped = (bool) $input->getOption('skip-mocked');

        $this->symfonyStyle->title('1. Detecting parent and entity classes');

        $phpFileInfos = PhpFilesFinder::find($paths);

        // double to count for both parent and entity resolver
        $stepRatio = $areMockedSkipped ? 3 : 2;

        $this->symfonyStyle->progressStart($stepRatio * count($phpFileInfos));

        $progressClosure = function (): void {
            $this->symfonyStyle->progressAdvance();
        };

        $parentClassNames = $this->parentClassResolver->resolve($phpFileInfos, $progressClosure);
        $entityClassNames = $this->entityClassResolver->resolve($paths, $progressClosure);

        $mockedClassNames = $areMockedSkipped ? $this->mockedClassResolver->resolve($paths, $progressClosure) : [];

        $this->symfonyStyle->progressFinish();

        $this->symfonyStyle->writeln(sprintf(
            'Found %d parent and %d entity classes',
            count($parentClassNames),
            count($entityClassNames)
        ));

        if ($areMockedSkipped) {
            $this->symfonyStyle->writeln(sprintf('Also %d mocked classes', count($mockedClassNames)));
        }

        $this->symfonyStyle->newLine(1);

        $this->symfonyStyle->title('2. Finalizing safe classes');

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
                'final class '
            );

            $finalizedFilePaths[] = PathHelper::relativeToCwd($phpFileInfo->getRealPath());

            if ($isDryRun === false) {
                FileSystem::write($phpFileInfo->getRealPath(), $finalizedContents);
            }
        }

        if ($finalizedFilePaths === []) {
            $this->symfonyStyle->success('Nothing to finalize');
            return self::SUCCESS;
        }

        $this->symfonyStyle->listing($finalizedFilePaths);

        $this->symfonyStyle->success(sprintf(
            '%d classes %s finalized',
            count($finalizedFilePaths),
            $isDryRun ? 'would be' : 'were'
        ));

        return Command::SUCCESS;
    }
}

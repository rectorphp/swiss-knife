<?php

namespace Rector\SwissKnife\Command;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use PhpParser\NodeTraverser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\Finalize\EntityClassResolver;
use TomasVotruba\Finalize\FileSystem\PhpFilesFinder;
use TomasVotruba\Finalize\NodeVisitor\NeedForFinalizeNodeVisitor;
use TomasVotruba\Finalize\ParentClassResolver;
use TomasVotruba\Finalize\PhpParser\CachedPhpParser;

final class FinalizeCommand extends Command
{
    /**
     * @see https://regex101.com/r/Q5Nfbo/1
     */
    public const NEWLINE_CLASS_START_REGEX = '#^class\s#m';

    public function __construct(
        private readonly SymfonyStyle $symfonyStyle,
        private readonly ParentClassResolver $parentClassResolver,
        private readonly EntityClassResolver $entityClassResolver,
        private readonly CachedPhpParser $cachedPhpParser
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('finalize');

        $this->setDescription('Generate class family tree and make all safe classes final');

        $this->addArgument('paths', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Directories to finalize');
    }

    /**
     * @return self::FAILURE|self::SUCCESS
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $paths = (array) $input->getArgument('paths');

        $phpFileInfos = PhpFilesFinder::findPhpFileInfos($paths);

        $this->symfonyStyle->title('1. Detecting parent and entity classes');

        // double to count for both parent and entity resolver
        $this->symfonyStyle->progressStart(2 * count($phpFileInfos));

        $progressClosure = function () {
            $this->symfonyStyle->progressAdvance();
        };

        $parentClassNames = $this->parentClassResolver->resolve($phpFileInfos, $progressClosure);
        $entityClassNames = $this->entityClassResolver->resolve($phpFileInfos, $progressClosure);

        $this->symfonyStyle->progressFinish();

        $this->symfonyStyle->writeln(sprintf(
            'Found %d parent and %d entity classes',
            count($parentClassNames),
            count($entityClassNames)
        ));

        $this->symfonyStyle->newLine(1);

        $this->symfonyStyle->title('2. Finalizing safe classes');

        // @todo create a simple service returning bool
        $finalizingNodeTraverser = new NodeTraverser();
        $needForFinalizeNodeVisitor = new NeedForFinalizeNodeVisitor(
            array_merge($parentClassNames, $entityClassNames)
        );

        $finalizingNodeTraverser->addVisitor($needForFinalizeNodeVisitor);

        $finalizedFilePaths = [];

        foreach ($phpFileInfos as $phpFileInfo) {
            // should be file be finalize, is not and is not excluded?
            $stmts = $this->cachedPhpParser->parseFile($phpFileInfo->getRealPath());
            $finalizingNodeTraverser->traverse($stmts);

            if (! $needForFinalizeNodeVisitor->isNeeded()) {
                continue;
            }

            $this->symfonyStyle->writeln(sprintf('File "%s" was finalized', $phpFileInfo->getRelativePath()));

            $finalizedContents = Strings::replace(
                $phpFileInfo->getContents(),
                self::NEWLINE_CLASS_START_REGEX,
                'final class '
            );

            $finalizedFilePaths[] = $phpFileInfo->getRelativePath();
            FileSystem::write($phpFileInfo->getRealPath(), $finalizedContents);
        }

        if ($finalizedFilePaths === []) {
            $this->symfonyStyle->success('Nothign to finalize');
            return self::SUCCESS;
        }

        $this->symfonyStyle->listing($finalizedFilePaths);
        $this->symfonyStyle->success(sprintf('%d classes were finalized', count($finalizedFilePaths)));

        return Command::SUCCESS;
    }
}

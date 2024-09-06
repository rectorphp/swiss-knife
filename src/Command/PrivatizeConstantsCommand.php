<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use PhpParser\NodeTraverser;
use Rector\SwissKnife\Contract\ClassConstantFetchInterface;
use Rector\SwissKnife\Finder\PhpFilesFinder;
use Rector\SwissKnife\PhpParser\CachedPhpParser;
use Rector\SwissKnife\PhpParser\ClassConstantFetchFinder;
use Rector\SwissKnife\PhpParser\NodeTraverserFactory;
use Rector\SwissKnife\PhpParser\NodeVisitor\FindNonPrivateClassConstNodeVisitor;
use Rector\SwissKnife\ValueObject\ClassConstant;
use Rector\SwissKnife\ValueObject\ClassConstantFetch\CurrentClassConstantFetch;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\SplFileInfo;

final class PrivatizeConstantsCommand extends Command
{
    public function __construct(
        private readonly SymfonyStyle $symfonyStyle,
        private readonly CachedPhpParser $cachedPhpParser,
        private readonly ClassConstantFetchFinder $classConstantFetchFinder,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('privatize-constants');

        $this->addArgument(
            'sources',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'One or more paths to check, include tests directory as well'
        );

        $this->addOption(
            'exclude-path',
            null,
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
            'Path to exclude'
        );

        $this->addOption('debug', null, InputOption::VALUE_NONE, 'Debug output');

        $this->setDescription('Make class constants private if not used outside');
    }

    /**
     * @return Command::*
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sources = (array) $input->getArgument('sources');
        $excludedPaths = (array) $input->getOption('exclude-path');
        $isDebug = (bool) $input->getOption('debug');

        $phpFileInfos = PhpFilesFinder::find($sources, $excludedPaths);
        if ($phpFileInfos === []) {
            $this->symfonyStyle->warning('No PHP files found in provided paths');

            return self::SUCCESS;
        }

        $this->symfonyStyle->note('1. Finding class const fetches...');

        $progressBar = $this->symfonyStyle->createProgressBar(count($phpFileInfos));
        $classConstantFetches = $this->classConstantFetchFinder->find($phpFileInfos, $progressBar, $isDebug);

        $this->symfonyStyle->newLine(2);
        $this->symfonyStyle->success(sprintf('Found %d class constant fetches', count($classConstantFetches)));

        // go file by file and deal with public + protected constants
        foreach ($phpFileInfos as $phpFileInfo) {
            $this->processFileInfo($phpFileInfo, $classConstantFetches);
        }

        return self::SUCCESS;
    }

    private function parseAndTraverseFile(SplFileInfo $phpFileInfo, NodeTraverser $nodeTraverser): void
    {
        $fileStmts = $this->cachedPhpParser->parseFile($phpFileInfo->getRealPath());
        $nodeTraverser->traverse($fileStmts);
    }

    /**
     * @param ClassConstantFetchInterface[] $classConstantFetches
     */
    private function processFileInfo(SplFileInfo $phpFileInfo, array $classConstantFetches): void
    {
        $findNonPrivateClassConstNodeVisitor = new FindNonPrivateClassConstNodeVisitor();
        $nodeTraverser = NodeTraverserFactory::create($findNonPrivateClassConstNodeVisitor);

        $this->parseAndTraverseFile($phpFileInfo, $nodeTraverser);

        // nothing found
        if ($findNonPrivateClassConstNodeVisitor->getClassConstants() === []) {
            return;
        }

        foreach ($findNonPrivateClassConstNodeVisitor->getClassConstants() as $classConstant) {
            if ($this->isClassConstantUsedPublicly($classConstantFetches, $classConstant)) {
                $changedFileContents = Strings::replace(
                    $phpFileInfo->getContents(),
                    '#(public\s+)?const\s+' . $classConstant->getConstantName() . '#',
                    'public const ' . $classConstant->getConstantName()
                );

                FileSystem::write($phpFileInfo->getRealPath(), $changedFileContents);

                $this->symfonyStyle->note(
                    sprintf('Constant "%s" changed to public', $classConstant->getConstantName())
                );
                continue;
            }

            // make private
            $changedFileContents = Strings::replace(
                $phpFileInfo->getContents(),
                '#((private|public|protected)\s+)?const\s+' . $classConstant->getConstantName() . '#',
                'private const ' . $classConstant->getConstantName()
            );
            FileSystem::write($phpFileInfo->getRealPath(), $changedFileContents);

            $this->symfonyStyle->note(sprintf('Constant "%s" changed to private', $classConstant->getConstantName()));
        }
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

<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use Rector\SwissKnife\Comments\CommentedCodeAnalyzer;
use Rector\SwissKnife\Finder\FilesFinder;
use SwissKnife202405\Symfony\Component\Console\Command\Command;
use SwissKnife202405\Symfony\Component\Console\Input\InputArgument;
use SwissKnife202405\Symfony\Component\Console\Input\InputInterface;
use SwissKnife202405\Symfony\Component\Console\Input\InputOption;
use SwissKnife202405\Symfony\Component\Console\Output\OutputInterface;
use SwissKnife202405\Symfony\Component\Console\Style\SymfonyStyle;
final class CheckCommentedCodeCommand extends Command
{
    /**
     * @readonly
     * @var \Rector\SwissKnife\Comments\CommentedCodeAnalyzer
     */
    private $commentedCodeAnalyzer;
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @var int
     */
    private const DEFAULT_LINE_LIMIT = 5;
    public function __construct(CommentedCodeAnalyzer $commentedCodeAnalyzer, SymfonyStyle $symfonyStyle)
    {
        $this->commentedCodeAnalyzer = $commentedCodeAnalyzer;
        $this->symfonyStyle = $symfonyStyle;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('check-commented-code');
        $this->addArgument('sources', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'One or more paths to check');
        $this->setDescription('Checks code for commented snippets');
        $this->addOption('line-limit', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_OPTIONAL, 'Amount of allowed comment lines in a row', self::DEFAULT_LINE_LIMIT);
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $sources = (array) $input->getArgument('sources');
        $phpFileInfos = FilesFinder::findPhpFiles($sources);
        $message = \sprintf('Analysing %d *.php files', \count($phpFileInfos));
        $this->symfonyStyle->note($message);
        $lineLimit = (int) $input->getOption('line-limit');
        $commentedLinesByFilePaths = [];
        foreach ($phpFileInfos as $phpFileInfo) {
            $commentedLines = $this->commentedCodeAnalyzer->process($phpFileInfo->getRealPath(), $lineLimit);
            if ($commentedLines === []) {
                continue;
            }
            $commentedLinesByFilePaths[$phpFileInfo->getRealPath()] = $commentedLines;
        }
        if ($commentedLinesByFilePaths === []) {
            $this->symfonyStyle->success('No commented code found');
            return self::SUCCESS;
        }
        foreach ($commentedLinesByFilePaths as $filePath => $commentedLines) {
            foreach ($commentedLines as $commentedLine) {
                $messageLine = ' * ' . $filePath . ':' . $commentedLine;
                $this->symfonyStyle->writeln($messageLine);
            }
        }
        $this->symfonyStyle->error('Errors found');
        return self::FAILURE;
    }
}

<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Command;

use EasyCI202301\Symfony\Component\Console\Input\InputArgument;
use EasyCI202301\Symfony\Component\Console\Input\InputInterface;
use EasyCI202301\Symfony\Component\Console\Input\InputOption;
use EasyCI202301\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Comments\CommentedCodeAnalyzer;
use Symplify\EasyCI\ValueObject\Option;
use EasyCI202301\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
final class CheckCommentedCodeCommand extends AbstractSymplifyCommand
{
    /**
     * @var int
     */
    private const DEFAULT_LINE_LIMIT = 5;
    /**
     * @var \Symplify\EasyCI\Comments\CommentedCodeAnalyzer
     */
    private $commentedCodeAnalyzer;
    public function __construct(CommentedCodeAnalyzer $commentedCodeAnalyzer)
    {
        $this->commentedCodeAnalyzer = $commentedCodeAnalyzer;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('check-commented-code');
        $this->addArgument(Option::SOURCES, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'One or more paths to check');
        $this->setDescription('Checks code for commented snippets');
        $this->addOption(Option::LINE_LIMIT, null, InputOption::VALUE_REQUIRED | InputOption::VALUE_OPTIONAL, 'Amount of allowed comment lines in a row', self::DEFAULT_LINE_LIMIT);
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $sources = (array) $input->getArgument(Option::SOURCES);
        $phpFileInfos = $this->smartFinder->find($sources, '*.php');
        $message = \sprintf('Analysing %d *.php files', \count($phpFileInfos));
        $this->symfonyStyle->note($message);
        $lineLimit = (int) $input->getOption(Option::LINE_LIMIT);
        $commentedLinesByFilePaths = [];
        foreach ($phpFileInfos as $phpFileInfo) {
            $commentedLines = $this->commentedCodeAnalyzer->process($phpFileInfo, $lineLimit);
            if ($commentedLines === []) {
                continue;
            }
            $commentedLinesByFilePaths[$phpFileInfo->getRelativeFilePathFromCwd()] = $commentedLines;
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

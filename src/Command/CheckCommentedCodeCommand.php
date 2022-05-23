<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Command;

use EasyCI20220523\Symfony\Component\Console\Input\InputArgument;
use EasyCI20220523\Symfony\Component\Console\Input\InputInterface;
use EasyCI20220523\Symfony\Component\Console\Input\InputOption;
use EasyCI20220523\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Comments\CommentedCodeAnalyzer;
use Symplify\EasyCI\ValueObject\Option;
use EasyCI20220523\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use EasyCI20220523\Symplify\PackageBuilder\Console\Command\CommandNaming;
final class CheckCommentedCodeCommand extends \EasyCI20220523\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand
{
    /**
     * @var int
     */
    private const DEFAULT_LINE_LIMIT = 5;
    /**
     * @var \Symplify\EasyCI\Comments\CommentedCodeAnalyzer
     */
    private $commentedCodeAnalyzer;
    public function __construct(\Symplify\EasyCI\Comments\CommentedCodeAnalyzer $commentedCodeAnalyzer)
    {
        $this->commentedCodeAnalyzer = $commentedCodeAnalyzer;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName(\EasyCI20220523\Symplify\PackageBuilder\Console\Command\CommandNaming::classToName(self::class));
        $this->addArgument(\Symplify\EasyCI\ValueObject\Option::SOURCES, \EasyCI20220523\Symfony\Component\Console\Input\InputArgument::REQUIRED | \EasyCI20220523\Symfony\Component\Console\Input\InputArgument::IS_ARRAY, 'One or more paths to check');
        $this->setDescription('Checks code for commented snippets');
        $this->addOption(\Symplify\EasyCI\ValueObject\Option::LINE_LIMIT, null, \EasyCI20220523\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED | \EasyCI20220523\Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, 'Amount of allowed comment lines in a row', self::DEFAULT_LINE_LIMIT);
    }
    protected function execute(\EasyCI20220523\Symfony\Component\Console\Input\InputInterface $input, \EasyCI20220523\Symfony\Component\Console\Output\OutputInterface $output) : int
    {
        $sources = (array) $input->getArgument(\Symplify\EasyCI\ValueObject\Option::SOURCES);
        $phpFileInfos = $this->smartFinder->find($sources, '*.php');
        $message = \sprintf('Analysing %d *.php files', \count($phpFileInfos));
        $this->symfonyStyle->note($message);
        $lineLimit = (int) $input->getOption(\Symplify\EasyCI\ValueObject\Option::LINE_LIMIT);
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

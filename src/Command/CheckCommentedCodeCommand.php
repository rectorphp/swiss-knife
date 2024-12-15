<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Rector\SwissKnife\Comments\CommentedCodeAnalyzer;
use Rector\SwissKnife\Finder\PhpFilesFinder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class CheckCommentedCodeCommand extends Command
{
    /**
     * @var int
     */
    private const DEFAULT_LINE_LIMIT = 5;

    public function __construct(
        private readonly CommentedCodeAnalyzer $commentedCodeAnalyzer,
        private readonly SymfonyStyle $symfonyStyle,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('check-commented-code');

        $this->addArgument(
            'sources',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'One or more paths to check'
        );
        $this->addOption('skip-file', null, InputOption::VALUE_REQUIRED, 'Skip file path');
        $this->setDescription('Checks code for commented snippets');

        $this->addOption(
            'line-limit',
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_OPTIONAL,
            'Amount of allowed comment lines in a row',
            self::DEFAULT_LINE_LIMIT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sources = (array) $input->getArgument('sources');
        $skipFiles = (array) $input->getOption('skip-file');

        $phpFileInfos = PhpFilesFinder::find($sources, $skipFiles);

        $message = sprintf('Analysing %d *.php files', count($phpFileInfos));
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

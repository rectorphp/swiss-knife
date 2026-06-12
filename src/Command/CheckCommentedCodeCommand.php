<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Output\OutputPrinter;
use Rector\SwissKnife\Comments\CommentedCodeAnalyzer;
use Rector\SwissKnife\Finder\PhpFilesFinder;

final readonly class CheckCommentedCodeCommand implements CommandInterface
{
    private const int DEFAULT_LINE_LIMIT = 5;

    public function __construct(
        private CommentedCodeAnalyzer $commentedCodeAnalyzer,
        private OutputPrinter $outputPrinter,
    ) {
    }

    /**
     * @param string[] $sources One or more paths to check
     * @param string[] $skipFiles File paths to skip
     * @param int $lineLimit Maximum number of comment lines in a row allowed
     *
     * @return ExitCode::*
     */
    public function run(array $sources, array $skipFiles = [], int $lineLimit = self::DEFAULT_LINE_LIMIT): int
    {
        $phpFileInfos = PhpFilesFinder::find($sources, $skipFiles);

        $message = sprintf('Analysing %d *.php files', count($phpFileInfos));
        $this->outputPrinter->yellow($message);

        $commentedLinesByFilePaths = [];
        foreach ($phpFileInfos as $phpFileInfo) {
            $commentedLines = $this->commentedCodeAnalyzer->process($phpFileInfo->getRealPath(), $lineLimit);

            if ($commentedLines === []) {
                continue;
            }

            $commentedLinesByFilePaths[$phpFileInfo->getRealPath()] = $commentedLines;
        }

        if ($commentedLinesByFilePaths === []) {
            $this->outputPrinter->success('No commented code found');
            return ExitCode::SUCCESS;
        }

        foreach ($commentedLinesByFilePaths as $filePath => $commentedLines) {
            foreach ($commentedLines as $commentedLine) {
                $messageLine = ' * ' . $filePath . ':' . $commentedLine;
                $this->outputPrinter->writeln($messageLine);
            }
        }

        $this->outputPrinter->error('Errors found');

        return ExitCode::ERROR;
    }

    public function getName(): string
    {
        return 'check-commented-code';
    }

    public function getDescription(): string
    {
        return 'Checks code for commented snippets';
    }
}

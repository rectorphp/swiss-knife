<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Output\OutputPrinter;
use Rector\SwissKnife\DuplicatedCode\CloneDetector;
use Rector\SwissKnife\Finder\PhpFilesFinder;

final readonly class DuplicatedCodeCommand implements CommandInterface
{
    private const int DEFAULT_MIN_LINES = 5;

    private const int DEFAULT_MIN_TOKENS = 70;

    public function __construct(
        private OutputPrinter $outputPrinter,
    ) {
    }

    /**
     * @param string[] $sources One or more paths to scan
     * @param string[] $skipFiles File paths to skip
     * @param int $minLines Minimum lines of a reported clone
     * @param int $minTokens Minimum tokens of a reported clone
     * @param bool $fuzzy Ignore variable names when matching
     *
     * @return ExitCode::*
     */
    public function run(
        array $sources,
        array $skipFiles = [],
        int $minLines = self::DEFAULT_MIN_LINES,
        int $minTokens = self::DEFAULT_MIN_TOKENS,
        bool $fuzzy = false
    ): int {
        $phpFileInfos = PhpFilesFinder::find($sources, $skipFiles);

        $filePaths = [];
        foreach ($phpFileInfos as $phpFileInfo) {
            $filePaths[] = $phpFileInfo->getRealPath();
        }

        $this->outputPrinter->yellow(sprintf('Scanning %d *.php files for duplicated code', count($filePaths)));

        $cloneDetector = new CloneDetector($minLines, $minTokens, $fuzzy);
        $clones = $cloneDetector->detect($filePaths);

        if ($clones === []) {
            $this->outputPrinter->green(sprintf('No duplicates found in %d files', count($filePaths)));
            return ExitCode::SUCCESS;
        }

        $duplicatedLines = 0;
        foreach ($clones as $clone) {
            $this->outputPrinter->writeln(sprintf(
                ' * %s:%d-%d (%d lines, %d tokens)',
                $clone->firstFile->filePath,
                $clone->firstFile->startLine,
                $clone->firstFile->endLine,
                $clone->lines,
                $clone->tokens
            ));
            $this->outputPrinter->writeln(sprintf(
                '   %s:%d-%d',
                $clone->secondFile->filePath,
                $clone->secondFile->startLine,
                $clone->secondFile->endLine
            ));
            $this->outputPrinter->newline();

            $duplicatedLines += $clone->lines;
        }

        $this->outputPrinter->redBackground(sprintf(
            'Found %d clones with %d duplicated lines in %d scanned files',
            count($clones),
            $duplicatedLines,
            count($filePaths)
        ));

        return ExitCode::ERROR;
    }

    public function getName(): string
    {
        return 'duplicated-code';
    }

    public function getDescription(): string
    {
        return 'Finds duplicated, copy-pasted code blocks via token-based detection';
    }
}

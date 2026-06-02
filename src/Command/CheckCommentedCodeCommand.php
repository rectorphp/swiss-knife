<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use SwissKnife202606\Entropy\Console\Contract\CommandInterface;
use SwissKnife202606\Entropy\Console\Enum\ExitCode;
use Rector\SwissKnife\Comments\CommentedCodeAnalyzer;
use Rector\SwissKnife\Finder\PhpFilesFinder;
use SwissKnife202606\Symfony\Component\Console\Style\SymfonyStyle;
final class CheckCommentedCodeCommand implements CommandInterface
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
    }
    /**
     * @param string[] $sources One or more paths to check
     * @param string[] $skipFiles File paths to skip
     * @param int $lineLimit Maximum number of comment lines in a row allowed
     *
     * @return ExitCode::*
     */
    public function run(array $sources, array $skipFiles = [], int $lineLimit = self::DEFAULT_LINE_LIMIT) : int
    {
        $phpFileInfos = PhpFilesFinder::find($sources, $skipFiles);
        $message = \sprintf('Analysing %d *.php files', \count($phpFileInfos));
        $this->symfonyStyle->note($message);
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
            return ExitCode::SUCCESS;
        }
        foreach ($commentedLinesByFilePaths as $filePath => $commentedLines) {
            foreach ($commentedLines as $commentedLine) {
                $messageLine = ' * ' . $filePath . ':' . $commentedLine;
                $this->symfonyStyle->writeln($messageLine);
            }
        }
        $this->symfonyStyle->error('Errors found');
        return ExitCode::ERROR;
    }
    public function getName() : string
    {
        return 'check-commented-code';
    }
    public function getDescription() : string
    {
        return 'Checks code for commented snippets';
    }
}

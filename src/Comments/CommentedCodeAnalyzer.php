<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Comments;

use Nette\Utils\FileSystem;

/**
 * @see \Symplify\EasyCI\Tests\Comments\CommentedCodeAnalyzerTest
 */
final class CommentedCodeAnalyzer
{
    /**
     * @return int[]
     */
    public function process(string $filePath, int $commentedLinesCountLimit): array
    {
        $commentedLines = [];

        $fileLines = explode(PHP_EOL, FileSystem::read($filePath));

        $commentLinesCount = 0;

        foreach ($fileLines as $key => $fileLine) {
            $isCommentLine = str_starts_with(trim($fileLine), '//');
            if ($isCommentLine) {
                ++$commentLinesCount;
            } else {
                // crossed the threshold?
                if ($commentLinesCount >= $commentedLinesCountLimit) {
                    $commentedLines[] = $key;
                }

                // reset counter
                $commentLinesCount = 0;
            }
        }

        return $commentedLines;
    }
}

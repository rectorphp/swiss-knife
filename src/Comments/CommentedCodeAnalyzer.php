<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Comments;

use SwissKnife202402\Nette\Utils\FileSystem;
/**
 * @see \Rector\SwissKnife\Tests\Comments\CommentedCodeAnalyzerTest
 */
final class CommentedCodeAnalyzer
{
    /**
     * @return int[]
     */
    public function process(string $filePath, int $commentedLinesCountLimit) : array
    {
        $commentedLines = [];
        $fileLines = \explode(\PHP_EOL, FileSystem::read($filePath));
        $commentLinesCount = 0;
        foreach ($fileLines as $key => $fileLine) {
            $isCommentLine = \strncmp(\trim($fileLine), '//', \strlen('//')) === 0;
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

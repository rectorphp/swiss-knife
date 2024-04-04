<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Comments;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;

/**
 * @see \Rector\SwissKnife\Tests\Comments\CommentedCodeAnalyzerTest
 */
final class CommentedCodeAnalyzer
{
    /**
     * @var string
     * @see https://regex101.com/r/5OlGjG/1
     * @see https://3v4l.org/Y8pSD
     */
    private const NEWLINE_REGEX = '#\r?\n#';

    /**
     * @return int[]
     */
    public function process(string $filePath, int $commentedLinesCountLimit): array
    {
        $commentedLines = [];

        $fileLines = Strings::split(FileSystem::read($filePath), self::NEWLINE_REGEX);

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

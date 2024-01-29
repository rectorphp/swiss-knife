<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Comments;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCI\Comments\CommentedCodeAnalyzer;

final class CommentedCodeAnalyzerTest extends TestCase
{
    private CommentedCodeAnalyzer $commentedCodeAnalyzer;

    protected function setUp(): void
    {
        $this->commentedCodeAnalyzer = new CommentedCodeAnalyzer();
    }

    public function test(): void
    {
        $filePath = __DIR__ . '/Fixture/some_commented_code.php.inc';
        $commentedLines = $this->commentedCodeAnalyzer->process($filePath, 4);
        $this->assertSame([], $commentedLines);

        $commentedLines = $this->commentedCodeAnalyzer->process($filePath, 2);
        $this->assertSame([5], $commentedLines);
    }
}

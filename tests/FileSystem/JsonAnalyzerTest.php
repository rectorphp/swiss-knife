<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\FileSystem;

use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\FileSystem\JsonAnalyzer;

final class JsonAnalyzerTest extends TestCase
{
    private JsonAnalyzer $jsonAnalyzer;

    protected function setUp(): void
    {
        $this->jsonAnalyzer = new JsonAnalyzer();
    }

    public function testIsPrettyPrinted(): void
    {
        $this->assertFalse($this->jsonAnalyzer->isPrettyPrinted('{"a":1}'));

        $prettyJson = "{\n    \"a\": 1\n}";
        $this->assertTrue($this->jsonAnalyzer->isPrettyPrinted($prettyJson));
    }
}

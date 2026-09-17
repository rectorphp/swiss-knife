<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\DuplicatedCode;

use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\DuplicatedCode\CloneDetector;

final class CloneDetectorTest extends TestCase
{
    public function testDetectsDuplicateAcrossFiles(): void
    {
        $cloneDetector = new CloneDetector(3, 25, false);

        $clones = $cloneDetector->detect([
            __DIR__ . '/Fixture/first_duplicate.php.inc',
            __DIR__ . '/Fixture/second_duplicate.php.inc',
        ]);

        $this->assertCount(1, $clones);

        $codeClone = $clones[0];
        $this->assertStringEndsWith('first_duplicate.php.inc', $codeClone->firstFile->filePath);
        $this->assertStringEndsWith('second_duplicate.php.inc', $codeClone->secondFile->filePath);
        $this->assertGreaterThanOrEqual(3, $codeClone->lines);
    }

    public function testDoesNotReportUniqueCode(): void
    {
        $cloneDetector = new CloneDetector(3, 25, false);

        $clones = $cloneDetector->detect([
            __DIR__ . '/Fixture/first_duplicate.php.inc',
            __DIR__ . '/Fixture/unique.php.inc',
        ]);

        $this->assertSame([], $clones);
    }
}

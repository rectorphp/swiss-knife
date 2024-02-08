<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Testing\UnitTestFilePathsFinder;

use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\Testing\Finder\TestCaseClassFinder;
use Rector\SwissKnife\Testing\UnitTestFilePathsFinder;
use Rector\SwissKnife\Testing\UnitTestFilter;
use Rector\SwissKnife\Tests\Testing\UnitTestFilePathsFinder\Fixture\OldSchoolTest;
use Rector\SwissKnife\Tests\Testing\UnitTestFilePathsFinder\Fixture\RandomTest;

final class UnitTestFilePathsFinderTest extends TestCase
{
    private UnitTestFilePathsFinder $unitTestFilePathsFinder;

    protected function setup(): void
    {
        $this->unitTestFilePathsFinder = new UnitTestFilePathsFinder(
            new TestCaseClassFinder(),
            new UnitTestFilter(),
        );
    }

    public function test(): void
    {
        $unitTestFilePaths = $this->unitTestFilePathsFinder->findInDirectories([__DIR__ . '/Fixture']);
        $this->assertCount(2, $unitTestFilePaths);

        $this->assertArrayHasKey(RandomTest::class, $unitTestFilePaths);
        $this->assertArrayHasKey(OldSchoolTest::class, $unitTestFilePaths);
    }
}

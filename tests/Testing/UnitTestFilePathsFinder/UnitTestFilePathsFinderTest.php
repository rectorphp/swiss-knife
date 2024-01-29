<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Testing\UnitTestFilePathsFinder;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCI\Testing\Finder\TestCaseClassFinder;
use Symplify\EasyCI\Testing\UnitTestFilePathsFinder;
use Symplify\EasyCI\Testing\UnitTestFilter;
use Symplify\EasyCI\Tests\Testing\UnitTestFilePathsFinder\Fixture\OldSchoolTest;
use Symplify\EasyCI\Tests\Testing\UnitTestFilePathsFinder\Fixture\RandomTest;

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

<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\SmokeTestgen\FileSystem;

use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\SmokeTestgen\FileSystem\TestsDirectoryResolver;

final class TestsDirectoryResolverTest extends TestCase
{
    private string $tempDirectory;

    protected function setUp(): void
    {
        $this->tempDirectory = sys_get_temp_dir() . '/swiss-knife-tests-dir-' . uniqid();
        FileSystem::createDir($this->tempDirectory);
    }

    protected function tearDown(): void
    {
        FileSystem::delete($this->tempDirectory);
    }

    public function testResolveWithUnitDirectory(): void
    {
        FileSystem::createDir($this->tempDirectory . '/tests/Unit');

        $testsDirectoryResolver = new TestsDirectoryResolver();
        $smokeDirectory = $testsDirectoryResolver->resolveSmokeUnitTestDirectory($this->tempDirectory);

        $this->assertSame('tests/Unit/Smoke', $smokeDirectory);
    }

    public function testResolveWithFallback(): void
    {
        $testsDirectoryResolver = new TestsDirectoryResolver();
        $smokeDirectory = $testsDirectoryResolver->resolveSmokeUnitTestDirectory($this->tempDirectory);

        $this->assertSame('tests/Unit/Smoke', $smokeDirectory);
    }
}

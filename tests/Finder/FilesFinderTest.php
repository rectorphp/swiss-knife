<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Finder;

use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\Finder\FilesFinder;

final class FilesFinderTest extends TestCase
{
    private string $originalCwd;

    protected function setUp(): void
    {
        $originalCwd = getcwd();
        $this->originalCwd = $originalCwd === false ? __DIR__ : $originalCwd;

        // find() resolves sources relative to the current working directory
        chdir(__DIR__);
    }

    protected function tearDown(): void
    {
        chdir($this->originalCwd);
    }

    public function testFindAll(): void
    {
        $files = FilesFinder::find(['FilesFinderFixture']);
        $this->assertCount(2, $files);
    }

    public function testExcludeByPath(): void
    {
        $files = FilesFinder::find(['FilesFinderFixture'], ['skip-dir']);

        $this->assertCount(1, $files);

        $file = array_pop($files);
        $this->assertNotNull($file);
        $this->assertStringContainsString('keep.txt', $file->getRealPath());
    }

    public function testExcludeByFnMatch(): void
    {
        $files = FilesFinder::find(['FilesFinderFixture'], ['*skipped.txt']);

        $this->assertCount(1, $files);

        $file = array_pop($files);
        $this->assertNotNull($file);
        $this->assertStringContainsString('keep.txt', $file->getRealPath());
    }
}

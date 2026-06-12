<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\FileSystem;

use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\FileSystem\PathHelper;

final class PathHelperTest extends TestCase
{
    private string $originalCwd;

    private string $tempDirectory;

    protected function setUp(): void
    {
        $originalCwd = getcwd();
        $this->originalCwd = $originalCwd === false ? __DIR__ : $originalCwd;

        $this->tempDirectory = sys_get_temp_dir() . '/swiss-knife-path-helper-' . uniqid();
        FileSystem::createDir($this->tempDirectory);
        chdir($this->tempDirectory);
    }

    protected function tearDown(): void
    {
        chdir($this->originalCwd);
        FileSystem::delete($this->tempDirectory);
    }

    public function testRelativeToCwd(): void
    {
        $filePath = $this->tempDirectory . '/some/nested/file.php';
        FileSystem::createDir(dirname($filePath));
        FileSystem::write($filePath, '<?php', null);

        $this->assertSame('some/nested/file.php', PathHelper::relativeToCwd($filePath));
    }
}

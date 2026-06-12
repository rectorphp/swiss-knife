<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Testing\Printer;

use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\Testing\Printer\PHPUnitXmlPrinter;

final class PHPUnitXmlPrinterTest extends TestCase
{
    private string $originalCwd;

    private string $tempDirectory;

    protected function setUp(): void
    {
        $originalCwd = getcwd();
        $this->originalCwd = $originalCwd === false ? __DIR__ : $originalCwd;

        $this->tempDirectory = sys_get_temp_dir() . '/swiss-knife-xml-printer-' . uniqid();
        FileSystem::createDir($this->tempDirectory);
        chdir($this->tempDirectory);
    }

    protected function tearDown(): void
    {
        chdir($this->originalCwd);
        FileSystem::delete($this->tempDirectory);
    }

    public function testPrintFiles(): void
    {
        $filePath = $this->tempDirectory . '/SomeTest.php';
        FileSystem::write($filePath, '<?php', null);

        $phpunitXmlPrinter = new PHPUnitXmlPrinter();
        $output = $phpunitXmlPrinter->printFiles([$filePath]);

        $this->assertSame('<file>SomeTest.php</file>' . PHP_EOL, $output);
    }
}

<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command;

use Entropy\Console\Enum\ExitCode;
use Nette\Utils\FileSystem;
use Rector\SwissKnife\Testing\Command\DetectUnitTestsCommand;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class DetectUnitTestsCommandTest extends AbstractTestCase
{
    private string $originalCwd;

    private string $tempDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        $originalCwd = getcwd();
        $this->originalCwd = $originalCwd === false ? __DIR__ : $originalCwd;

        $this->tempDirectory = sys_get_temp_dir() . '/swiss-knife-detect-unit-' . uniqid();
        FileSystem::createDir($this->tempDirectory);
        chdir($this->tempDirectory);
    }

    protected function tearDown(): void
    {
        @unlink($this->tempDirectory . '/phpunit-unit-files.xml');
        chdir($this->originalCwd);
        FileSystem::delete($this->tempDirectory);
    }

    public function testRunWithUnitTests(): void
    {
        FileSystem::copy(
            __DIR__ . '/../Testing/UnitTestFilePathsFinder/Fixture/RandomTest.php',
            $this->tempDirectory . '/RandomTest.php'
        );

        $command = $this->make(DetectUnitTestsCommand::class);
        $exitCode = $command->run([$this->tempDirectory]);

        $this->assertSame(ExitCode::SUCCESS, $exitCode);
        $this->assertFileExists($this->tempDirectory . '/phpunit-unit-files.xml');
    }

    public function testRunWithNoUnitTests(): void
    {
        $command = $this->make(DetectUnitTestsCommand::class);
        $exitCode = $command->run([$this->tempDirectory]);

        $this->assertSame(ExitCode::SUCCESS, $exitCode);
    }
}

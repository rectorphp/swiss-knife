<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command;

use Entropy\Console\Enum\ExitCode;
use Nette\Utils\FileSystem;
use Rector\SwissKnife\Command\DumpEditorconfigCommand;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class DumpEditorconfigCommandTest extends AbstractTestCase
{
    private string $originalCwd;

    private string $tempDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        $originalCwd = getcwd();
        $this->originalCwd = $originalCwd === false ? __DIR__ : $originalCwd;

        $this->tempDirectory = sys_get_temp_dir() . '/swiss-knife-editorconfig-' . uniqid();
        FileSystem::createDir($this->tempDirectory);
        chdir($this->tempDirectory);
    }

    protected function tearDown(): void
    {
        chdir($this->originalCwd);
        FileSystem::delete($this->tempDirectory);
    }

    public function testSuccess(): void
    {
        $command = $this->make(DumpEditorconfigCommand::class);

        $exitCode = $command->run();

        $this->assertSame(ExitCode::SUCCESS, $exitCode);
        $this->assertFileExists($this->tempDirectory . '/.editorconfig');
    }

    public function testErrorWhenAlreadyExists(): void
    {
        FileSystem::write($this->tempDirectory . '/.editorconfig', 'root = true', null);

        $command = $this->make(DumpEditorconfigCommand::class);
        $exitCode = $command->run();

        $this->assertSame(ExitCode::ERROR, $exitCode);
    }
}

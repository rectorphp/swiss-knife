<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command;

use Entropy\Console\Enum\ExitCode;
use Nette\Utils\FileSystem;
use Rector\SwissKnife\Command\CheckConflictsCommand;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class CheckConflictsCommandTest extends AbstractTestCase
{
    private string $originalCwd;

    protected function setUp(): void
    {
        parent::setUp();

        $originalCwd = getcwd();
        $this->originalCwd = $originalCwd === false ? __DIR__ : $originalCwd;
        chdir(__DIR__);
    }

    protected function tearDown(): void
    {
        chdir($this->originalCwd);
    }

    public function testSuccessWhenNoConflicts(): void
    {
        $command = $this->make(CheckConflictsCommand::class);

        $exitCode = $command->run(['Fixture/CheckConflicts/no-conflicts']);

        $this->assertSame(ExitCode::SUCCESS, $exitCode);
    }

    public function testErrorWhenConflictsFound(): void
    {
        $tempDirectory = sys_get_temp_dir() . '/swiss-knife-check-conflicts-' . uniqid();
        FileSystem::createDir($tempDirectory);
        FileSystem::write($tempDirectory . '/conflict.txt', "<<<<<<< HEAD\n=======\n", null);

        try {
            chdir($tempDirectory);

            $command = $this->make(CheckConflictsCommand::class);
            $exitCode = $command->run(['.']);

            $this->assertSame(ExitCode::ERROR, $exitCode);
        } finally {
            chdir($this->originalCwd);
            FileSystem::delete($tempDirectory);
        }
    }
}

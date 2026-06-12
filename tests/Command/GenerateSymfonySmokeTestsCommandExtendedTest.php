<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command;

use Entropy\Console\Enum\ExitCode;
use Nette\Utils\FileSystem;
use Rector\SwissKnife\Command\GenerateSymfonySmokeTestsCommand;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class GenerateSymfonySmokeTestsCommandExtendedTest extends AbstractTestCase
{
    private string $originalCwd;

    private string $tempDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        $originalCwd = getcwd();
        $this->originalCwd = $originalCwd === false ? __DIR__ : $originalCwd;

        $this->tempDirectory = sys_get_temp_dir() . '/swiss-knife-smoke-error-' . uniqid();
        FileSystem::createDir($this->tempDirectory);
        FileSystem::createDir($this->tempDirectory . '/tests/Unit/Smoke');
        FileSystem::write($this->tempDirectory . '/composer.json', '{"require":{"some/package":"^1.0"}}', null);
        chdir($this->tempDirectory);
    }

    protected function tearDown(): void
    {
        chdir($this->originalCwd);
        FileSystem::delete($this->tempDirectory);
    }

    public function testNoMatchingPackages(): void
    {
        $command = $this->make(GenerateSymfonySmokeTestsCommand::class);

        $this->assertSame(ExitCode::ERROR, $command->run());
    }
}

<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command;

use Entropy\Console\Enum\ExitCode;
use Nette\Utils\FileSystem;
use Rector\SwissKnife\Command\SplitSymfonyConfigToPerPackageCommand;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class SplitSymfonyConfigToPerPackageCommandTest extends AbstractTestCase
{
    private string $tempDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tempDirectory = sys_get_temp_dir() . '/swiss-knife-split-config-' . uniqid();
        FileSystem::createDir($this->tempDirectory . '/packages');
        FileSystem::copy(
            __DIR__ . '/Fixture/SplitSymfonyConfig/config.php',
            $this->tempDirectory . '/config.php'
        );
    }

    protected function tearDown(): void
    {
        FileSystem::delete($this->tempDirectory);
    }

    public function testRun(): void
    {
        $command = $this->make(SplitSymfonyConfigToPerPackageCommand::class);

        $exitCode = $command->run(
            $this->tempDirectory . '/config.php',
            $this->tempDirectory . '/packages'
        );

        $this->assertSame(ExitCode::SUCCESS, $exitCode);
        $this->assertFileExists($this->tempDirectory . '/packages/framework.php');
        $this->assertFileExists($this->tempDirectory . '/packages/doctrine.php');
    }
}

<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command;

use Entropy\Console\Enum\ExitCode;
use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use Rector\SwissKnife\Command\GenerateSymfonySmokeTestsCommand;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class GenerateSymfonySmokeTestsCommandTest extends AbstractTestCase
{
    private string $originalCwd;

    private string $tempDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        $originalCwd = getcwd();
        $this->originalCwd = $originalCwd === false ? __DIR__ : $originalCwd;

        $this->tempDirectory = sys_get_temp_dir() . '/swiss-knife-smoke-tests-' . uniqid();
        FileSystem::createDir($this->tempDirectory . '/tests/Unit/Smoke');

        FileSystem::write($this->tempDirectory . '/composer.json', Json::encode([
            'require' => [
                'symfony/dependency-injection' => '^7.0',
            ],
            'autoload-dev' => [
                'psr-4' => [
                    'App\\Tests\\' => 'tests/',
                ],
            ],
        ]), null);

        chdir($this->tempDirectory);
    }

    protected function tearDown(): void
    {
        chdir($this->originalCwd);
        FileSystem::delete($this->tempDirectory);
    }

    public function testRun(): void
    {
        $command = $this->make(GenerateSymfonySmokeTestsCommand::class);

        $exitCode = $command->run();

        $this->assertSame(ExitCode::SUCCESS, $exitCode);
        $this->assertFileExists($this->tempDirectory . '/tests/Unit/Smoke/ServiceContainerTest.php');
        $this->assertFileExists($this->tempDirectory . '/tests/Unit/Smoke/AbstractContainerTestCase.php');
    }
}

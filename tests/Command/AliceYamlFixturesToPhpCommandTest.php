<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command;

use Entropy\Console\Enum\ExitCode;
use Nette\Utils\FileSystem;
use Rector\SwissKnife\Command\AliceYamlFixturesToPhpCommand;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class AliceYamlFixturesToPhpCommandTest extends AbstractTestCase
{
    private string $tempDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tempDirectory = sys_get_temp_dir() . '/swiss-knife-alice-' . uniqid();
        FileSystem::createDir($this->tempDirectory);
    }

    protected function tearDown(): void
    {
        FileSystem::delete($this->tempDirectory);
    }

    public function testRun(): void
    {
        $yamlPath = $this->tempDirectory . '/fixture.yml';
        FileSystem::write($yamlPath, "SomeEntity:\n    name: test\n", null);

        $command = $this->make(AliceYamlFixturesToPhpCommand::class);
        $exitCode = $command->run([$this->tempDirectory]);

        $this->assertSame(ExitCode::SUCCESS, $exitCode);
        $this->assertFileExists($this->tempDirectory . '/fixture.php');
        $this->assertFileDoesNotExist($yamlPath);
    }
}

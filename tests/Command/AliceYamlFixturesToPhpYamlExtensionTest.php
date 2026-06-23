<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command;

use Entropy\Console\Enum\ExitCode;
use Nette\Utils\FileSystem;
use Rector\SwissKnife\Command\AliceYamlFixturesToPhpCommand;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class AliceYamlFixturesToPhpYamlExtensionTest extends AbstractTestCase
{
    private string $tempDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tempDirectory = sys_get_temp_dir() . '/swiss-knife-alice-yaml-' . uniqid();
        FileSystem::createDir($this->tempDirectory);
    }

    protected function tearDown(): void
    {
        FileSystem::delete($this->tempDirectory);
    }

    public function testYamlExtension(): void
    {
        $yamlPath = $this->tempDirectory . '/fixture.yaml';
        FileSystem::write($yamlPath, "SomeEntity:\n    name: test\n", null);

        $command = $this->make(AliceYamlFixturesToPhpCommand::class);
        $exitCode = $command->run([$this->tempDirectory]);

        $this->assertSame(ExitCode::SUCCESS, $exitCode);
        $this->assertFileExists($this->tempDirectory . '/fixture.php');
    }
}

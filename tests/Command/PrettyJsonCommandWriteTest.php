<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command;

use Entropy\Console\Enum\ExitCode;
use Rector\SwissKnife\Command\PrettyJsonCommand;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class PrettyJsonCommandWriteTest extends AbstractTestCase
{
    private string $tempDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tempDirectory = sys_get_temp_dir() . '/swiss-knife-pretty-json-' . uniqid();
        \Nette\Utils\FileSystem::createDir($this->tempDirectory);
        \Nette\Utils\FileSystem::write($this->tempDirectory . '/ugly.json', '{"a":1}', null);
    }

    protected function tearDown(): void
    {
        \Nette\Utils\FileSystem::delete($this->tempDirectory);
    }

    public function testWritePrettyJson(): void
    {
        $command = $this->make(PrettyJsonCommand::class);

        $exitCode = $command->run([$this->tempDirectory . '/ugly.json'], dryRun: false);

        $this->assertSame(ExitCode::SUCCESS, $exitCode);
        $this->assertStringContainsString("\n", \Nette\Utils\FileSystem::read($this->tempDirectory . '/ugly.json'));
    }
}

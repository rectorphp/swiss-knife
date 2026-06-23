<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command;

use Entropy\Console\Enum\ExitCode;
use Rector\SwissKnife\Command\PrettyJsonCommand;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class PrettyJsonCommandTest extends AbstractTestCase
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

    public function testDryRun(): void
    {
        $command = $this->make(PrettyJsonCommand::class);

        $exitCode = $command->run(['Fixture/PrettyJson'], dryRun: true);

        $this->assertSame(ExitCode::SUCCESS, $exitCode);
    }

    public function testErrorWhenNoJsonFiles(): void
    {
        $command = $this->make(PrettyJsonCommand::class);

        $exitCode = $command->run(['Fixture/PrettyJson/empty-dir']);

        $this->assertSame(ExitCode::ERROR, $exitCode);
    }
}

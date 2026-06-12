<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command;

use Entropy\Console\Enum\ExitCode;
use Rector\SwissKnife\Command\FinalizeClassesCommand;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class FinalizeClassesCommandTest extends AbstractTestCase
{
    public function testDryRun(): void
    {
        $command = $this->make(FinalizeClassesCommand::class);

        $exitCode = $command->run(
            [__DIR__ . '/Fixture/FinalizeClasses'],
            dryRun: true,
            noProgress: true
        );

        $this->assertContains($exitCode, [ExitCode::SUCCESS, ExitCode::ERROR]);
    }

    public function testDryRunWithSkipMocked(): void
    {
        $command = $this->make(FinalizeClassesCommand::class);

        $exitCode = $command->run(
            [__DIR__ . '/Fixture/FinalizeClasses'],
            dryRun: true,
            skipMocked: true,
            noProgress: true
        );

        $this->assertContains($exitCode, [ExitCode::SUCCESS, ExitCode::ERROR]);
    }
}

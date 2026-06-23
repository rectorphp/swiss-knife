<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command;

use Entropy\Console\Enum\ExitCode;
use Rector\SwissKnife\Command\PrivatizeConstantsCommand;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class PrivatizeConstantsCommandTest extends AbstractTestCase
{
    public function testDryRun(): void
    {
        $command = $this->make(PrivatizeConstantsCommand::class);

        $exitCode = $command->run(
            [__DIR__ . '/Fixture/PrivatizeConstants'],
            dryRun: true
        );

        $this->assertContains($exitCode, [ExitCode::SUCCESS, ExitCode::ERROR]);
    }

    public function testEmptySources(): void
    {
        $tempDirectory = sys_get_temp_dir() . '/swiss-knife-privatize-empty-' . uniqid();
        mkdir($tempDirectory);

        try {
            $command = $this->make(PrivatizeConstantsCommand::class);
            $exitCode = $command->run([$tempDirectory]);

            $this->assertSame(ExitCode::SUCCESS, $exitCode);
        } finally {
            rmdir($tempDirectory);
        }
    }
}

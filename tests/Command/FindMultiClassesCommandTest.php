<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command;

use Entropy\Console\Enum\ExitCode;
use Rector\SwissKnife\Command\FindMultiClassesCommand;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class FindMultiClassesCommandTest extends AbstractTestCase
{
    public function testSuccessWhenNoMultipleClasses(): void
    {
        $command = $this->make(FindMultiClassesCommand::class);

        $exitCode = $command->run([__DIR__ . '/Fixture/FindMultiClasses/single'], []);

        $this->assertSame(ExitCode::SUCCESS, $exitCode);
    }

    public function testErrorWhenMultipleClassesFound(): void
    {
        $command = $this->make(FindMultiClassesCommand::class);

        $exitCode = $command->run([__DIR__ . '/../Finder/MultiClassFixture'], []);

        $this->assertSame(ExitCode::ERROR, $exitCode);
    }
}

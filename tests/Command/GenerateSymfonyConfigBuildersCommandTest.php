<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command;

use Entropy\Console\Enum\ExitCode;
use Rector\SwissKnife\Command\GenerateSymfonyConfigBuildersCommand;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class GenerateSymfonyConfigBuildersCommandTest extends AbstractTestCase
{
    public function testRun(): void
    {
        $command = $this->make(GenerateSymfonyConfigBuildersCommand::class);

        $this->assertSame(ExitCode::ERROR, $command->run());
    }
}

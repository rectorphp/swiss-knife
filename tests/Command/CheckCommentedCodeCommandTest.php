<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command;

use Entropy\Console\Enum\ExitCode;
use Rector\SwissKnife\Command\CheckCommentedCodeCommand;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class CheckCommentedCodeCommandTest extends AbstractTestCase
{
    public function testSuccessWhenNoCommentedCode(): void
    {
        $command = $this->make(CheckCommentedCodeCommand::class);

        $exitCode = $command->run([__DIR__ . '/Fixture/CheckCommentedCode/Clean'], [], 4);

        $this->assertSame(ExitCode::SUCCESS, $exitCode);
    }

    public function testErrorWhenCommentedCodeFound(): void
    {
        $command = $this->make(CheckCommentedCodeCommand::class);

        $exitCode = $command->run([__DIR__ . '/Fixture/CheckCommentedCode/Commented'], [], 2);

        $this->assertSame(ExitCode::ERROR, $exitCode);
    }
}

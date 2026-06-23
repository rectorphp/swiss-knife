<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command;

use Entropy\Console\Enum\ExitCode;
use Rector\SwissKnife\Command\SearchRegexCommand;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class SearchRegexCommandTest extends AbstractTestCase
{
    public function testRun(): void
    {
        $command = $this->make(SearchRegexCommand::class);

        $exitCode = $command->run(
            '#class SearchRegexTarget#',
            __DIR__ . '/Fixture/SearchRegex'
        );

        $this->assertSame(ExitCode::SUCCESS, $exitCode);
    }
}

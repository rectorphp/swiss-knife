<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command;

use Entropy\Console\Enum\ExitCode;
use Rector\SwissKnife\Command\SearchRegexCommand;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class SearchRegexDefaultDirectoryTest extends AbstractTestCase
{
    private string $originalCwd;

    protected function setUp(): void
    {
        parent::setUp();

        $originalCwd = getcwd();
        $this->originalCwd = $originalCwd === false ? __DIR__ : $originalCwd;
        chdir(__DIR__ . '/Fixture/SearchRegex');
    }

    protected function tearDown(): void
    {
        chdir($this->originalCwd);
    }

    public function testDefaultProjectDirectory(): void
    {
        $command = $this->make(SearchRegexCommand::class);

        $exitCode = $command->run('#class SearchRegexTarget#');

        $this->assertSame(ExitCode::SUCCESS, $exitCode);
    }
}

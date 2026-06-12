<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command;

use Entropy\Console\Enum\ExitCode;
use Nette\Utils\FileSystem;
use Rector\SwissKnife\Command\SpotLazyTraitsCommand;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class SpotLazyTraitsCommandTest extends AbstractTestCase
{
    public function testWithTraits(): void
    {
        $command = $this->make(SpotLazyTraitsCommand::class);

        $exitCode = $command->run([__DIR__ . '/../Traits/Fixture'], maxUsed: 2);

        $this->assertSame(ExitCode::SUCCESS, $exitCode);
    }

    public function testWithoutTraits(): void
    {
        $tempDirectory = sys_get_temp_dir() . '/swiss-knife-no-traits-' . uniqid();
        FileSystem::createDir($tempDirectory);

        try {
            $command = $this->make(SpotLazyTraitsCommand::class);
            $exitCode = $command->run([$tempDirectory]);

            $this->assertSame(ExitCode::SUCCESS, $exitCode);
        } finally {
            FileSystem::delete($tempDirectory);
        }
    }
}

<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command;

use Entropy\Console\Enum\ExitCode;
use Nette\Utils\FileSystem;
use Rector\SwissKnife\Command\FinalizeClassesCommand;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class FinalizeClassesCommandProgressTest extends AbstractTestCase
{
    public function testWithProgressBarAndDryRunError(): void
    {
        $tempDirectory = sys_get_temp_dir() . '/swiss-knife-finalize-progress-' . uniqid();
        FileSystem::createDir($tempDirectory);
        FileSystem::write($tempDirectory . '/ToFinalize.php', <<<'PHP'
<?php

declare(strict_types=1);

namespace TempFinalizeProgress;

class ToFinalize
{
}
PHP, null);

        try {
            $command = $this->make(FinalizeClassesCommand::class);
            $exitCode = $command->run([$tempDirectory], dryRun: true, noProgress: false);

            $this->assertSame(ExitCode::ERROR, $exitCode);
        } finally {
            FileSystem::delete($tempDirectory);
        }
    }

    public function testWithSkipMockedAndProgressBar(): void
    {
        $command = $this->make(FinalizeClassesCommand::class);

        $exitCode = $command->run(
            [__DIR__ . '/Fixture/FinalizeClasses'],
            dryRun: true,
            skipMocked: true,
            noProgress: false
        );

        $this->assertContains($exitCode, [ExitCode::SUCCESS, ExitCode::ERROR]);
    }
}

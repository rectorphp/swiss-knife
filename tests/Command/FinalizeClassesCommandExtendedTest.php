<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command;

use Entropy\Console\Enum\ExitCode;
use Nette\Utils\FileSystem;
use Rector\SwissKnife\Command\FinalizeClassesCommand;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class FinalizeClassesCommandExtendedTest extends AbstractTestCase
{
    public function testFinalize(): void
    {
        $tempDirectory = sys_get_temp_dir() . '/swiss-knife-finalize-' . uniqid();
        FileSystem::createDir($tempDirectory);
        FileSystem::write($tempDirectory . '/ToFinalize.php', <<<'PHP'
<?php

declare(strict_types=1);

namespace TempFinalize;

class ToFinalize
{
}
PHP, null);

        try {
            $command = $this->make(FinalizeClassesCommand::class);
            $exitCode = $command->run([$tempDirectory], dryRun: false, noProgress: true);

            $this->assertSame(ExitCode::SUCCESS, $exitCode);
            $this->assertStringContainsString('final class', FileSystem::read($tempDirectory . '/ToFinalize.php'));
        } finally {
            FileSystem::delete($tempDirectory);
        }
    }
}

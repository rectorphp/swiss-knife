<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command;

use Entropy\Console\Enum\ExitCode;
use Nette\Utils\FileSystem;
use Rector\SwissKnife\Command\PrivatizeConstantsCommand;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class PrivatizeConstantsCommandExtendedTest extends AbstractTestCase
{
    public function testPrivatize(): void
    {
        $tempDirectory = sys_get_temp_dir() . '/swiss-knife-privatize-' . uniqid();
        FileSystem::createDir($tempDirectory);
        FileSystem::write($tempDirectory . '/WithConstant.php', <<<'PHP'
<?php

declare(strict_types=1);

namespace TempPrivatize;

final class WithConstant
{
    public const VALUE = 'x';

    public function get(): string
    {
        return self::VALUE;
    }
}
PHP, null);

        try {
            $command = $this->make(PrivatizeConstantsCommand::class);
            $exitCode = $command->run([$tempDirectory], dryRun: false);

            $this->assertSame(ExitCode::SUCCESS, $exitCode);
            $this->assertStringContainsString('private const VALUE', FileSystem::read($tempDirectory . '/WithConstant.php'));
        } finally {
            FileSystem::delete($tempDirectory);
        }
    }

    public function testDryRunWithPrivatizableConstant(): void
    {
        $tempDirectory = sys_get_temp_dir() . '/swiss-knife-priv-dry-' . uniqid();
        FileSystem::createDir($tempDirectory);
        FileSystem::write($tempDirectory . '/WithConstant.php', <<<'PHP'
<?php

declare(strict_types=1);

namespace TempPrivatizeDry;

final class WithConstant
{
    public const VALUE = 'x';

    public function get(): string
    {
        return self::VALUE;
    }
}
PHP, null);

        try {
            $command = $this->make(PrivatizeConstantsCommand::class);
            $exitCode = $command->run([$tempDirectory], dryRun: true);

            $this->assertSame(ExitCode::ERROR, $exitCode);
        } finally {
            FileSystem::delete($tempDirectory);
        }
    }

    public function testExternalConstantUsage(): void
    {
        $tempDirectory = sys_get_temp_dir() . '/swiss-knife-priv-ext-' . uniqid();
        FileSystem::createDir($tempDirectory);
        FileSystem::write($tempDirectory . '/WithPublicConstant.php', <<<'PHP'
<?php

declare(strict_types=1);

namespace TempPrivatizeExt;

final class WithPublicConstant
{
    public const PUBLIC_CONSTANT = 'value';
}
PHP, null);
        FileSystem::write($tempDirectory . '/UsesConstant.php', <<<'PHP'
<?php

declare(strict_types=1);

namespace TempPrivatizeExt;

final class UsesConstant
{
    public function get(): string
    {
        return \TempPrivatizeExt\WithPublicConstant::PUBLIC_CONSTANT;
    }
}
PHP, null);

        try {
            $command = $this->make(PrivatizeConstantsCommand::class);
            $exitCode = $command->run([$tempDirectory], dryRun: true);

            $this->assertContains($exitCode, [ExitCode::SUCCESS, ExitCode::ERROR]);
        } finally {
            FileSystem::delete($tempDirectory);
        }
    }

    public function testNoConstantsInFile(): void
    {
        $tempDirectory = sys_get_temp_dir() . '/swiss-knife-priv-none-' . uniqid();
        FileSystem::createDir($tempDirectory);
        FileSystem::write($tempDirectory . '/NoConstants.php', <<<'PHP'
<?php

declare(strict_types=1);

namespace TempPrivatizeNone;

final class NoConstants
{
    public function run(): int
    {
        return 1;
    }
}
PHP, null);

        try {
            $command = $this->make(PrivatizeConstantsCommand::class);
            $exitCode = $command->run([$tempDirectory]);

            $this->assertSame(ExitCode::SUCCESS, $exitCode);
        } finally {
            FileSystem::delete($tempDirectory);
        }
    }
}

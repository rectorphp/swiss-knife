<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command;

use Entropy\Console\Enum\ExitCode;
use Nette\Utils\FileSystem;
use Rector\SwissKnife\Command\SplitSymfonyConfigToPerPackageCommand;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class SplitSymfonyConfigNoExtensionsTest extends AbstractTestCase
{
    public function testNoExtensions(): void
    {
        $tempDirectory = sys_get_temp_dir() . '/swiss-knife-no-ext-' . uniqid();
        FileSystem::createDir($tempDirectory);
        FileSystem::write($tempDirectory . '/config.php', <<<'PHP'
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import('services.php');
};
PHP, null);

        try {
            $command = $this->make(SplitSymfonyConfigToPerPackageCommand::class);
            $exitCode = $command->run($tempDirectory . '/config.php', $tempDirectory . '/out');

            $this->assertSame(ExitCode::SUCCESS, $exitCode);
        } finally {
            FileSystem::delete($tempDirectory);
        }
    }
}

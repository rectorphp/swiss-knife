<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command;

use Entropy\Console\Mapper\CLIRequestMapper;
use Entropy\Console\ValueObject\CLIRequest;
use Rector\SwissKnife\Command\NamespaceToPSR4Command;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class NamespaceToPSR4CommandTest extends AbstractTestCase
{
    private string $originalCwd;

    private CLIRequestMapper $cliRequestMapper;

    private NamespaceToPSR4Command $namespaceToPSR4Command;

    protected function setUp(): void
    {
        parent::setUp();

        $originalCwd = getcwd();
        $this->originalCwd = $originalCwd === false ? __DIR__ : $originalCwd;

        $this->cliRequestMapper = $this->make(CLIRequestMapper::class);
        $this->namespaceToPSR4Command = $this->make(NamespaceToPSR4Command::class);
    }

    protected function tearDown(): void
    {
        chdir($this->originalCwd);
    }

    /**
     * @see https://github.com/rectorphp/swiss-knife/issues/124
     */
    public function testNamespaceRootOptionIsKeptAsString(): void
    {
        // the input parser collects long-option values into arrays;
        // the string parameter must receive the single value, not the array cast to "Array"
        $cliRequest = new CLIRequest('namespace-to-psr-4', ['app'], [
            'namespace-root' => ['App\\'],
        ]);

        $arguments = $this->cliRequestMapper->resolveArguments($this->namespaceToPSR4Command, $cliRequest);

        $this->assertSame(['app', 'App\\'], $arguments);
    }

    public function testRunFixesIncorrectNamespace(): void
    {
        $tempDirectory = sys_get_temp_dir() . '/swiss-knife-namespace-' . uniqid();
        \Nette\Utils\FileSystem::createDir($tempDirectory . '/Sub');
        \Nette\Utils\FileSystem::write(
            $tempDirectory . '/Sub/SomeClass.php',
            <<<'PHP'
<?php

declare(strict_types=1);

namespace Wrong\Namespace;

final class SomeClass
{
}
PHP,
            null
        );

        chdir($tempDirectory);

        $exitCode = $this->namespaceToPSR4Command->run('.', 'App\\Tests');

        $this->assertSame(\Entropy\Console\Enum\ExitCode::SUCCESS, $exitCode);

        $contents = file_get_contents($tempDirectory . '/Sub/SomeClass.php');
        $this->assertStringContainsString('namespace App\\Tests\\Sub;', (string) $contents);

        \Nette\Utils\FileSystem::delete($tempDirectory);
    }

    public function testRunWithCorrectNamespace(): void
    {
        $tempDirectory = sys_get_temp_dir() . '/swiss-knife-namespace-ok-' . uniqid();
        \Nette\Utils\FileSystem::createDir($tempDirectory);
        \Nette\Utils\FileSystem::write(
            $tempDirectory . '/SomeClass.php',
            <<<'PHP'
<?php

declare(strict_types=1);

namespace App\\Tests;

final class SomeClass
{
}
PHP,
            null
        );

        chdir($tempDirectory);

        $exitCode = $this->namespaceToPSR4Command->run('.', 'App\\Tests');

        $this->assertSame(\Entropy\Console\Enum\ExitCode::SUCCESS, $exitCode);

        \Nette\Utils\FileSystem::delete($tempDirectory);
    }
}

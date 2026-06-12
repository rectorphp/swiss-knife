<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\SmokeTestgen\Templating;

use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\SmokeTestgen\Templating\TemplateDecorator;

final class TemplateDecoratorFallbackTest extends TestCase
{
    private string $originalCwd;

    private string $tempDirectory;

    protected function setUp(): void
    {
        $originalCwd = getcwd();
        $this->originalCwd = $originalCwd === false ? __DIR__ : $originalCwd;

        $this->tempDirectory = sys_get_temp_dir() . '/swiss-knife-template-fallback-' . uniqid();
        FileSystem::createDir($this->tempDirectory);
        chdir($this->tempDirectory);
    }

    protected function tearDown(): void
    {
        chdir($this->originalCwd);
        FileSystem::delete($this->tempDirectory);
    }

    public function testFallbackNamespaceWithoutComposerJson(): void
    {
        $templateDecorator = new TemplateDecorator();
        $decorated = $templateDecorator->decorate('__KERNEL_CLASS_PLACEHOLDER__ __SMOKE_TEST_NAMESPACE__');

        $this->assertStringContainsString('App\\Tests\\Unit\\Smoke', $decorated);
        $this->assertStringContainsString('Kernel', $decorated);
    }
}

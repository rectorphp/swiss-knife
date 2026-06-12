<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\SmokeTestgen\Templating;

use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\SmokeTestgen\Templating\TemplateDecorator;

final class TemplateDecoratorTest extends TestCase
{
    private string $originalCwd;

    private string $tempDirectory;

    protected function setUp(): void
    {
        $originalCwd = getcwd();
        $this->originalCwd = $originalCwd === false ? __DIR__ : $originalCwd;

        $this->tempDirectory = sys_get_temp_dir() . '/swiss-knife-template-decorator-' . uniqid();
        FileSystem::createDir($this->tempDirectory);
        chdir($this->tempDirectory);
    }

    protected function tearDown(): void
    {
        chdir($this->originalCwd);
        FileSystem::delete($this->tempDirectory);
    }

    public function testDecorate(): void
    {
        FileSystem::write($this->tempDirectory . '/composer.json', Json::encode([
            'autoload-dev' => [
                'psr-4' => [
                    'App\\Tests\\' => 'tests/',
                ],
            ],
        ]), null);

        $templateDecorator = new TemplateDecorator();
        $decorated = $templateDecorator->decorate(
            'namespace __SMOKE_TEST_NAMESPACE__; class __KERNEL_CLASS_PLACEHOLDER__Test {}'
        );

        $this->assertStringContainsString('App\\Tests\\Unit\\Smoke', $decorated);
        $this->assertStringNotContainsString('__SMOKE_TEST_NAMESPACE__', $decorated);
        $this->assertStringNotContainsString('__KERNEL_CLASS_PLACEHOLDER__', $decorated);
    }
}

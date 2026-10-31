<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command;

use Entropy\Utils\FileSystem;
use Rector\SwissKnife\Command\PrivatizeConstantsCommand;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class PrivatizeConstantsCommandTest extends AbstractTestCase
{
    private PrivatizeConstantsCommand $privatizeConstantsCommand;

    private string $tempDirectory;

    private string $tempFilePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->privatizeConstantsCommand = $this->make(PrivatizeConstantsCommand::class);

        $this->tempDirectory = sys_get_temp_dir() . '/swiss_knife_privatize_' . uniqid();
        $this->tempFilePath = $this->tempDirectory . '/TypedConstant.php';

        FileSystem::ensureDirectoryExists($this->tempDirectory);
        FileSystem::write($this->tempFilePath, FileSystem::read(__DIR__ . '/Fixture/typed_constant.php.inc'));
    }

    protected function tearDown(): void
    {
        FileSystem::delete($this->tempDirectory);
    }

    /**
     * @see https://github.com/rectorphp/swiss-knife/issues/160
     */
    public function testPrivatizesTypedConstantAndKeepsType(): void
    {
        $this->privatizeConstantsCommand->run([$this->tempDirectory]);

        $fileContents = FileSystem::read($this->tempFilePath);

        // typed constant must be privatized with its type kept
        $this->assertStringContainsString('private const string FOO', $fileContents);

        // regular constant must stay working too
        $this->assertStringContainsString('private const BAR', $fileContents);
    }
}

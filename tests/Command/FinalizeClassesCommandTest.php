<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command;

use Rector\SwissKnife\Command\FinalizeClassesCommand;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class FinalizeClassesCommandTest extends AbstractTestCase
{
    private FinalizeClassesCommand $finalizeClassesCommand;

    protected function setUp(): void
    {
        parent::setUp();

        $this->finalizeClassesCommand = $this->make(FinalizeClassesCommand::class);
    }

    public function testSkipDoctrineEntitiesByDefault(): void
    {
        $filePathsToFinalize = $this->finalizeClassesCommand->resolveFilePathsToFinalize([
            __DIR__ . '/Fixture/FinalizeClasses',
        ]);

        $this->assertSame([__DIR__ . '/Fixture/FinalizeClasses/SomeService.php'], $filePathsToFinalize);
    }

    public function testFinalizeDoctrineEntities(): void
    {
        $filePathsToFinalize = $this->finalizeClassesCommand->resolveFilePathsToFinalize(
            [__DIR__ . '/Fixture/FinalizeClasses'],
            false,
            [],
            true
        );

        $this->assertSame([
            __DIR__ . '/Fixture/FinalizeClasses/Entity/SomeEntity.php',
            __DIR__ . '/Fixture/FinalizeClasses/SomeService.php',
        ], $filePathsToFinalize);
    }

    /**
     * @dataProvider provideComposerJsonFiles
     */
    public function testDoctrineEntitiesFinalizeSupport(string $composerJsonFilePath, bool $expectedIsSupported): void
    {
        $this->assertSame(
            $expectedIsSupported,
            $this->finalizeClassesCommand->isDoctrineEntitiesFinalizeSupported($composerJsonFilePath)
        );
    }

    /**
     * @return iterable<array{string, bool}>
     */
    public static function provideComposerJsonFiles(): iterable
    {
        yield [__DIR__ . '/Fixture/Composer/supported-composer.json', true];
        yield [__DIR__ . '/Fixture/Composer/php-too-low-composer.json', false];
        yield [__DIR__ . '/Fixture/Composer/doctrine-dev-too-low-composer.json', false];
        yield [__DIR__ . '/Fixture/Composer/missing-composer.json', false];
    }
}

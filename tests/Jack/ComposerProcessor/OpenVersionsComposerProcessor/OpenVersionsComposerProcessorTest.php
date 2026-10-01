<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Jack\ComposerProcessor\OpenVersionsComposerProcessor;

use Entropy\Utils\FileSystem;
use Rector\SwissKnife\Jack\ComposerProcessor\OpenVersionsComposerProcessor;
use Rector\SwissKnife\Jack\ValueObject\ChangedPackageVersion;
use Rector\SwissKnife\Jack\ValueObject\OutdatedComposer;
use Rector\SwissKnife\Jack\ValueObject\OutdatedPackage;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class OpenVersionsComposerProcessorTest extends AbstractTestCase
{
    private OpenVersionsComposerProcessor $openVersionsComposerProcessor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->openVersionsComposerProcessor = $this->make(OpenVersionsComposerProcessor::class);
    }

    public function test(): void
    {
        $composerJsonContents = FileSystem::read(__DIR__ . '/Fixture/some-closed-composer.json');

        $outdatedComposer = new OutdatedComposer([
            new OutdatedPackage('nette/utils', '3.4.0', '^3.4', true, '4.1.0', '1 year'),
        ]);

        $changedPackageVersionsResult = $this->openVersionsComposerProcessor->process(
            $composerJsonContents,
            $outdatedComposer,
            10,
            false,
            null
        );

        $this->assertCount(1, $changedPackageVersionsResult->getChangedPackageVersions());
        $this->assertContainsOnlyInstancesOf(
            ChangedPackageVersion::class,
            $changedPackageVersionsResult->getChangedPackageVersions()
        );

        $this->assertStringEqualsFile(
            __DIR__ . '/Fixture/expected-opened-composer.json',
            $changedPackageVersionsResult->getComposerJsonContents()
        );
    }

    public function testSkipDev(): void
    {
        $composerJsonContents = FileSystem::read(__DIR__ . '/Fixture/skip-dev.json');

        $outdatedComposer = new OutdatedComposer([
            new OutdatedPackage('nette/utils', '5.4.0', 'dev-main', true, '6.4.0', '1 year'),
        ]);

        $changedPackageVersionsResult = $this->openVersionsComposerProcessor->process(
            $composerJsonContents,
            $outdatedComposer,
            10,
            false,
            null
        );

        $this->assertEmpty($changedPackageVersionsResult->getChangedPackageVersions());
    }
}

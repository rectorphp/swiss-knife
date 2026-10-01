<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Jack\ComposerProcessor\RaiseToInstalledComposerProcessor;

use Composer\Semver\VersionParser;
use Entropy\Utils\FileSystem;
use Rector\SwissKnife\Jack\Composer\InstalledVersionResolver;
use Rector\SwissKnife\Jack\ComposerProcessor\RaiseToInstalledComposerProcessor;
use Rector\SwissKnife\Jack\ValueObject\ChangedPackageVersion;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class RaiseToInstalledComposerProcessorTest extends AbstractTestCase
{
    private RaiseToInstalledComposerProcessor $raiseToInstalledComposerProcessor;

    protected function setUp(): void
    {
        parent::setUp();

        // hermetic installed versions, so the test does not depend on the real vendor/ state
        $installedVersionResolver = new InstalledVersionResolver(__DIR__ . '/Fixture/installed.json');
        $this->raiseToInstalledComposerProcessor = new RaiseToInstalledComposerProcessor(
            new VersionParser(),
            $installedVersionResolver
        );
    }

    public function test(): void
    {
        $composerJsonContents = FileSystem::read(__DIR__ . '/Fixture/some-outdated-composer.json');

        $changedPackageVersionsResult = $this->raiseToInstalledComposerProcessor->process($composerJsonContents);

        $this->assertCount(1, $changedPackageVersionsResult->getChangedPackageVersions());

        $this->assertContainsOnlyInstancesOf(
            ChangedPackageVersion::class,
            $changedPackageVersionsResult->getChangedPackageVersions()
        );

        $changedPackageVersion = $changedPackageVersionsResult->getChangedPackageVersions()[0];

        $this->assertSame('nette/utils', $changedPackageVersion->getPackageName());
        $this->assertSame('^2.0', $changedPackageVersion->getOldVersion());

        // note: this might change in near future; improve to dynamic soon
        $this->assertStringStartsWith('^4.1', $changedPackageVersion->getNewVersion());
    }

    public function testSkipDev(): void
    {
        $composerJsonContents = FileSystem::read(__DIR__ . '/Fixture/skip-dev.json');
        $changedPackageVersionsResult = $this->raiseToInstalledComposerProcessor->process($composerJsonContents);

        $this->assertEmpty($changedPackageVersionsResult->getChangedPackageVersions());
    }

    /**
     * @return iterable<array{string, string}>
     */
    public static function provideSkipSuggestChangeFiles(): iterable
    {
        yield [
            __DIR__ . '/Fixture/skip-suggest.json',
            <<<'JSON'
            {
                "require-dev": {
                    "nette/utils": "^4.1"
                },
                "suggest": {
                    "nette/utils": "to use container"
                }
            }

            JSON
        ];

        yield [
            __DIR__ . '/Fixture/skip-suggest-early-definition.json',
            <<<'JSON'
            {
                "suggest": {
                    "nette/utils": "to use container"
                },
                "require-dev": {
                    "nette/utils": "^4.1"
                }
            }

            JSON
        ];
    }

    /**
     * @dataProvider provideSkipSuggestChangeFiles
     */
    public function testSkipSuggestChange(string $file, string $changedFileContent): void
    {
        $composerJsonContents = FileSystem::read($file);

        $changedPackageVersionsResult = $this->raiseToInstalledComposerProcessor->process($composerJsonContents);

        $changedPackageVersion = $changedPackageVersionsResult->getChangedPackageVersions()[0];

        $this->assertSame('nette/utils', $changedPackageVersion->getPackageName());
        $this->assertSame('^3.4', $changedPackageVersion->getOldVersion());
        $this->assertSame('^4.1', $changedPackageVersion->getNewVersion());

        $this->assertSame($changedFileContent, $changedPackageVersionsResult->getComposerJsonContents());
    }

    public function testSkipConflictChange(): void
    {
        $composerJsonContents = FileSystem::read(__DIR__ . '/Fixture/skip-conflict.json');

        $changedPackageVersionsResult = $this->raiseToInstalledComposerProcessor->process($composerJsonContents);

        $changedPackageVersion = $changedPackageVersionsResult->getChangedPackageVersions()[0];

        $this->assertSame('nette/utils', $changedPackageVersion->getPackageName());
        $this->assertSame('^3.4', $changedPackageVersion->getOldVersion());
        $this->assertSame('^4.1', $changedPackageVersion->getNewVersion());

        $this->assertSame(
            <<<'JSON'
            {
                "require-dev": {
                    "nette/utils": "^4.1"
                },
                "conflict": {
                    "nette/utils": "<3.4"
                }
            }

            JSON
            ,
            $changedPackageVersionsResult->getComposerJsonContents()
        );
    }

    public function testSinglePiped(): void
    {
        $composerJsonContents = FileSystem::read(__DIR__ . '/Fixture/single-piped.json');

        $changedPackageVersionsResult = $this->raiseToInstalledComposerProcessor->process($composerJsonContents);

        $this->assertCount(1, $changedPackageVersionsResult->getChangedPackageVersions());
        $changedPackageVersion = $changedPackageVersionsResult->getChangedPackageVersions()[0];

        $this->assertInstanceOf(ChangedPackageVersion::class, $changedPackageVersion);

        $this->assertSame('nette/utils', $changedPackageVersion->getPackageName());
        $this->assertSame('^3.0 | ^4.0', $changedPackageVersion->getOldVersion());
    }

    public function testDoublePiped(): void
    {
        $composerJsonContents = FileSystem::read(__DIR__ . '/Fixture/double-piped.json');

        $changedPackageVersionsResult = $this->raiseToInstalledComposerProcessor->process($composerJsonContents);

        $this->assertCount(1, $changedPackageVersionsResult->getChangedPackageVersions());
        $changedPackageVersion = $changedPackageVersionsResult->getChangedPackageVersions()[0];

        $this->assertInstanceOf(ChangedPackageVersion::class, $changedPackageVersion);

        $this->assertSame('nette/utils', $changedPackageVersion->getPackageName());
        $this->assertSame('^3.0 | ^4.0', $changedPackageVersion->getOldVersion());
    }
}

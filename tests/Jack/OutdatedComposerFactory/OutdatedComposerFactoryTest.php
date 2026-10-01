<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Jack\OutdatedComposerFactory;

use Rector\SwissKnife\Jack\OutdatedComposerFactory;
use Rector\SwissKnife\Jack\ValueObject\OutdatedPackage;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class OutdatedComposerFactoryTest extends AbstractTestCase
{
    public function test(): void
    {
        $outdatedComposerFactory = $this->make(OutdatedComposerFactory::class);

        $outdatedComposer = $outdatedComposerFactory->createOutdatedComposer([
            [
                'name' => 'symfony/console',
                'direct-dependency' => true,
                'homepage' => 'https://symfony.com',
                'source' => 'https://github.com/symfony/console/tree/v6.4.20',
                'version' => 'v6.4.20',
                'release-age' => '1 month old',
                'release-date' => '2025-03-03T17:16:38+00:00',
                'latest' => 'v7.2.6',
                'latest-status' => 'update-possible',
                'latest-release-date' => '2025-04-07T19:09:28+00:00',
                'description' => 'Eases the creation of beautiful and testable command line interfaces',
                'abandoned' => false,
            ],
        ], __DIR__ . '/Fixture/some-composer.json');

        $this->assertCount(1, $outdatedComposer->getProdPackages());
        $this->assertContainsOnlyInstancesOf(OutdatedPackage::class, $outdatedComposer->getProdPackages());

        $this->assertCount(0, $outdatedComposer->getDevPackages());
    }

    public function testSkipsPackageWithSameCurrentAndLatestVersion(): void
    {
        $outdatedComposerFactory = $this->make(OutdatedComposerFactory::class);

        $outdatedComposer = $outdatedComposerFactory->createOutdatedComposer([
            [
                'name' => 'phpecs/phpecs',
                'direct-dependency' => true,
                'homepage' => 'https://github.com/easy-coding-standard/easy-coding-standard',
                'source' => 'https://github.com/easy-coding-standard/easy-coding-standard/tree/2.3.0',
                'version' => '2.3.0',
                'release-age' => '5 months old',
                'release-date' => '2026-01-12T10:00:00+00:00',
                'latest' => '2.3',
                'latest-status' => 'update-possible',
                'latest-release-date' => '2026-01-12T10:00:00+00:00',
                'description' => 'Use coding standard fixers and sniffs with 0-knowledge',
                'abandoned' => false,
            ],
        ], __DIR__ . '/Fixture/some-composer.json');

        $this->assertCount(0, $outdatedComposer->getPackages());
    }
}

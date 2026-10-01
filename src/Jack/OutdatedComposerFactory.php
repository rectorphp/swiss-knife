<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Jack;

use Rector\SwissKnife\Jack\Composer\VersionComparator;
use Rector\SwissKnife\Jack\Mapper\OutdatedPackageMapper;
use Rector\SwissKnife\Jack\ValueObject\OutdatedComposer;
use Rector\SwissKnife\Jack\ValueObject\OutdatedPackage;

/**
 * @see \Rector\SwissKnife\Jack\Tests\OutdatedComposerFactory\OutdatedComposerFactoryTest
 */
final class OutdatedComposerFactory
{
    private OutdatedPackageMapper $outdatedPackageMapper;

    public function __construct(OutdatedPackageMapper $outdatedPackageMapper)
    {
        $this->outdatedPackageMapper = $outdatedPackageMapper;
    }

    /**
     * @param mixed[] $installedPackages
     */
    public function createOutdatedComposer(array $installedPackages, string $composerJsonFilePath): OutdatedComposer
    {
        $outdatedPackages = $this->outdatedPackageMapper->mapToObjects($installedPackages, $composerJsonFilePath);

        // filter out dev packages, those are silently added, when "minimum-stability" is set to "dev"
        // filter out false positives, where the latest version is the same as the current one
        $nonDevOutdatedPackages = array_filter(
            $outdatedPackages,
            fn (OutdatedPackage $outdatedPackage): bool => ! $outdatedPackage->lastestIsDevBranch()
                && ! VersionComparator::areVersionsEqual(
                    $outdatedPackage->getCurrentVersion(),
                    $outdatedPackage->getLatestVersion()
                )
        );

        return new OutdatedComposer($nonDevOutdatedPackages);
    }
}

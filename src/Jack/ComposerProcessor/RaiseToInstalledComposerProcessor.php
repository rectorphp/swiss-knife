<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Jack\ComposerProcessor;

use Composer\Semver\Comparator;
use Composer\Semver\VersionParser;
use Entropy\Utils\Json;
use Rector\SwissKnife\Jack\Composer\InstalledVersionResolver;
use Rector\SwissKnife\Jack\Composer\VersionComparator;
use Rector\SwissKnife\Jack\FileSystem\ComposerJsonPackageVersionUpdater;
use Rector\SwissKnife\Jack\ValueObject\ChangedPackageVersion;
use Rector\SwissKnife\Jack\ValueObject\ComposerProcessorResult\ChangedPackageVersionsResult;

final class RaiseToInstalledComposerProcessor
{
    private VersionParser $versionParser;

    private InstalledVersionResolver $installedVersionResolver;

    public function __construct(VersionParser $versionParser, InstalledVersionResolver $installedVersionResolver)
    {
        $this->versionParser = $versionParser;
        $this->installedVersionResolver = $installedVersionResolver;
    }

    public function process(string $composerJsonContents): ChangedPackageVersionsResult
    {
        $installedPackagesToVersions = $this->installedVersionResolver->resolve();
        $composerJson = Json::decode($composerJsonContents);

        $changedPackageVersions = [];

        // iterate require and require-dev sections and check if installed version is newer one than in composer.json
        // if so, replace it
        $requiredPackagesToVersions = array_merge($composerJson['require'] ?? [], $composerJson['require-dev'] ?? []);
        foreach ($requiredPackagesToVersions as $packageName => $packageVersion) {
            if (! isset($installedPackagesToVersions[$packageName])) {
                continue;
            }

            if ($this->shouldSkipPackageVersion($packageVersion)) {
                continue;
            }

            $installedVersion = $installedPackagesToVersions[$packageName];

            // normalize pipe
            $packageVersion = str_replace('||', '|', $packageVersion);

            // special case for unions
            if (strpos((string) $packageVersion, '|') !== false) {
                $passingVersionKeys = [];

                $unionPackageVersions = explode('|', (string) $packageVersion);
                foreach ($unionPackageVersions as $key => $unionPackageVersion) {
                    $unionPackageConstraint = $this->versionParser->parseConstraints($unionPackageVersion);

                    if (Comparator::greaterThanOrEqualTo(
                        $installedVersion,
                        $unionPackageConstraint->getLowerBound()
                            ->getVersion()
                    )) {
                        $passingVersionKeys[] = $key;
                    }
                }

                // nothing we can do, as lower union version is passing
                if ($passingVersionKeys === [0]) {
                    continue;
                }

                // higher version is meet, let's drop the lower one
                if ($passingVersionKeys === [0, 1]) {
                    $newPackageVersion = $unionPackageVersions[1];

                    $composerJsonContents = ComposerJsonPackageVersionUpdater::update(
                        $composerJsonContents,
                        $packageName,
                        $newPackageVersion
                    );

                    $changedPackageVersions[] = new ChangedPackageVersion(
                        $packageName,
                        $packageVersion,
                        $newPackageVersion
                    );
                    continue;
                }
            }

            $normalizedInstalledVersion = $this->versionParser->normalize($installedVersion);
            $installedPackageConstraint = $this->versionParser->parseConstraints($packageVersion);

            $normalizedConstraintVersion = $this->versionParser->normalize(
                $installedPackageConstraint->getLowerBound()
                    ->getVersion()
            );

            // remove "-dev" suffix
            $normalizedConstraintVersion = str_replace('-dev', '', $normalizedConstraintVersion);

            // are major + minor equal?
            if (VersionComparator::areAndMinorVersionsEqual(
                $normalizedConstraintVersion,
                $normalizedInstalledVersion
            )) {
                continue;
            }

            [$major, $minor, $patch] = explode('.', $normalizedInstalledVersion);

            $newRequiredVersion = sprintf('^%s.%s', $major, $minor);

            // lets update
            $composerJsonContents = ComposerJsonPackageVersionUpdater::update(
                $composerJsonContents,
                $packageName,
                $newRequiredVersion
            );

            // focus on minor only
            // or on patch in case of 0.*
            $changedPackageVersions[] = new ChangedPackageVersion($packageName, $packageVersion, $newRequiredVersion);
        }

        return new ChangedPackageVersionsResult($composerJsonContents, $changedPackageVersions);
    }

    private function shouldSkipPackageVersion(string $packageVersion): bool
    {
        return strpos($packageVersion, 'dev-') !== false;
    }
}

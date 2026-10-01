<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Jack\Composer;

final class VersionComparator
{
    public static function areAndMinorVersionsEqual(string $firstVersion, string $secondVersion): bool
    {
        [$firstMajor, $firstMinor] = explode('.', $firstVersion);
        [$secondMajor, $secondMinor] = explode('.', $secondVersion);

        // if major and minor are equal, we can skip the update
        return $firstMajor === $secondMajor && $firstMinor === $secondMinor;
    }

    public static function areVersionsEqual(string $firstVersion, string $secondVersion): bool
    {
        return self::normalizeVersion($firstVersion) === self::normalizeVersion($secondVersion);
    }

    /**
     * Make versions comparable, e.g. "v2.3" and "2.3.0" are the same version
     */
    private static function normalizeVersion(string $version): string
    {
        $version = ltrim($version, 'v');

        while (preg_match('#^\d+(\.\d+)?$#', $version) === 1) {
            $version .= '.0';
        }

        return $version;
    }
}

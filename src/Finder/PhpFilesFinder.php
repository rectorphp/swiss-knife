<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Finder;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Webmozart\Assert\Assert;

final class PhpFilesFinder
{
    /**
     * @param string[] $paths
     * @param string[] $excludedPaths
     *
     * @return SplFileInfo[]
     */
    public static function find(array $paths, array $excludedPaths = []): array
    {
        $finder = self::createFinderForPathsAndExcludedPaths($paths, $excludedPaths);

        return iterator_to_array($finder->getIterator());
    }

    /**
     * @param string[] $paths
     * @param string[] $excludedPaths
     */
    private static function createFinderForPathsAndExcludedPaths(array $paths, array $excludedPaths): Finder
    {
        Assert::allString($paths);
        Assert::allFileExists($paths);

        $excludedFileNames = [];
        foreach($excludedPaths as $excludedPath) {
            if (!str_contains($excludedPath, '*')) {
                $excludedFileNames[] = $excludedPath;
            }
        }
        Assert::allFileExists($excludedFileNames);
        Assert::allString($excludedPaths);

        return Finder::create()
            ->files()
            ->in($paths)
            ->name('*.php')
            ->notPath('vendor')
            ->notPath('var')
            ->notPath('data-fixtures')
            ->notPath('node_modules')
            // exclude paths, as notPaths() does no work
            ->filter(static function (SplFileInfo $splFileInfo) use ($excludedPaths): bool {
                foreach ($excludedPaths as $excludedPath) {
                    $realpath = $splFileInfo->getRealPath();
                    if (str_contains($realpath, $excludedPath)) {
                        return false;
                    }

                    if (str_contains($excludedPath, '*') && \fnmatch($excludedPath, $realpath)) {
                        return false;
                    }
                }

                return true;
            });
    }
}

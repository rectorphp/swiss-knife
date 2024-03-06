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
     * @return SplFileInfo[]
     */
    public static function find(array $paths, array $excludedPaths = []): array
    {
        Assert::allString($paths);
        Assert::allFileExists($paths);

        Assert::allString($excludedPaths);
        Assert::allFileExists($excludedPaths);

        $finder = Finder::create()
            ->files()
            ->in($paths)
            ->name('*.php')
            // exclude paths, as notPaths() does no work
            ->filter(static function (SplFileInfo $splFileInfo) use ($excludedPaths): bool {
                foreach ($excludedPaths as $excludedPath) {
                    if (str_contains($splFileInfo->getRealPath(), $excludedPath)) {
                        return false;
                    }
                }
                return true;
            });

        return iterator_to_array($finder->getIterator());
    }
}

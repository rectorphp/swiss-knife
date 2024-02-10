<?php

declare(strict_types=1);

namespace TomasVotruba\Finalize\FileSystem;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class PhpFilesFinder
{
    /**
     * @param string[] $paths
     * @return SplFileInfo[]
     */
    public static function findPhpFileInfos(array $paths): array
    {
        $phpFinder = Finder::create()
            ->files()
            ->in($paths)
            ->name('*.php');

        return iterator_to_array($phpFinder);
    }
}

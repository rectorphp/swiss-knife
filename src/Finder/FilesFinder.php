<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Finder;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class FilesFinder
{
    /**
     * @param string[] $sources
     * @return SplFileInfo[]
     */
    public static function find(array $sources): array
    {
        $paths = [];
        foreach ($sources as $source) {
            $paths[] = getcwd() . DIRECTORY_SEPARATOR . $source;
        }

        $finder = Finder::create()
            ->files()
            ->in($paths)
            ->sortByName();

        return iterator_to_array($finder->getIterator());
    }

    /**
     * @param string[] $sources
     * @return SplFileInfo[]
     */
    public static function findPhpFiles(array $sources): array
    {
        $paths = [];
        foreach ($sources as $source) {
            $paths[] = getcwd() . DIRECTORY_SEPARATOR . $source;
        }

        $finder = Finder::create()
            ->files()
            ->in($paths)
            ->name('*.php')
            ->notPath('vendor')
            ->sortByName();

        return iterator_to_array($finder->getIterator());
    }
}

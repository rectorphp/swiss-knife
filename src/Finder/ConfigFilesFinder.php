<?php

declare(strict_types=1);

namespace TomasVotruba\Lemonade\Finder;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class ConfigFilesFinder
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
            ->name('*.php')
            ->path(['config'])
            ->notPath(['vendor', 'utils', 'var', 'packages'])
            ->sortByName();

        return iterator_to_array($finder->getIterator());
    }

    /**
     * @param string[] $sources
     * @return SplFileInfo[]
     */
    public static function findServices(array $sources): array
    {
        $fileInfos = self::find($sources);

        // exclude extension configuration configs
        return array_filter(
            $fileInfos,
            fn (SplFileInfo $fileInfo): bool => str_contains($fileInfo->getContents(), 'ContainerConfigurator')
        );
    }
}

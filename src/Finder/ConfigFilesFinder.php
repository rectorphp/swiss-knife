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
    private static function find(array $sources): array
    {
        $finder = Finder::create()
            ->files()
            ->in($sources)
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
    public static function findServices(string $projectDirectory): array
    {
        $fileInfos = self::find([$projectDirectory]);

        // exclude extension configuration configs
        return array_filter(
            $fileInfos,
            fn (SplFileInfo $fileInfo): bool => str_contains($fileInfo->getContents(), 'ContainerConfigurator')
        );
    }
}

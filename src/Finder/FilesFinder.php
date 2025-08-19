<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Finder;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Webmozart\Assert\Assert;

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
            // not our code
            ->notPath('node_modules')
            ->notPath('vendor')
            ->notPath('var/cache')
            ->sortByName();

        return iterator_to_array($finder->getIterator());
    }

    /**
     * @param string[] $directories
     * @return SplFileInfo[]
     */
    public static function findTwigFiles(array $directories): array
    {
        Assert::allString($directories);
        Assert::allDirectory($directories);

        $twigFinder = Finder::create()
            ->files()
            ->name('*.twig')
            ->in($directories)
            ->sortByName();

        return iterator_to_array($twigFinder->getIterator());
    }

    /**
     * @param string[] $sources
     * @return SplFileInfo[]
     */
    public static function findJsonFiles(array $sources): array
    {
        $jsonFileInfos = [];
        $directories = [];

        foreach ($sources as $source) {
            if (is_file($source)) {
                $jsonFileInfos[] = new SplFileInfo($source, '', $source);
            } else {
                $directories[] = $source;
            }
        }

        $jsonFileFinder = Finder::create()
            ->files()
            ->in($directories)
            ->name('*.json')
            ->sortByName();

        foreach ($jsonFileFinder->getIterator() as $fileInfo) {
            $jsonFileInfos[] = $fileInfo;
        }

        return $jsonFileInfos;
    }

    /**
     * @param string[] $paths
     * @return SplFileInfo[]
     */
    public static function findYamlFiles(array $paths): array
    {
        Assert::allString($paths);
        Assert::allFileExists($paths);

        $finder = Finder::create()
            ->files()
            ->in($paths)
            ->name('*.yml')
            ->name('*.yaml');

        return iterator_to_array($finder);
    }
}

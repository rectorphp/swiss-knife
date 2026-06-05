<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Finder;

use SwissKnife202606\Symfony\Component\Finder\Finder;
use SwissKnife202606\Symfony\Component\Finder\SplFileInfo;
use SwissKnife202606\Webmozart\Assert\Assert;
final class FilesFinder
{
    /**
     * @param string[] $sources
     * @param string[] $excludedPaths
     * @return SplFileInfo[]
     */
    public static function find(array $sources, array $excludedPaths = []) : array
    {
        $paths = [];
        foreach ($sources as $source) {
            $paths[] = \getcwd() . \DIRECTORY_SEPARATOR . $source;
        }
        $finder = Finder::create()->files()->in($paths)->notPath('node_modules')->notPath('vendor')->notPath('var/cache')->sortByName();
        if ($excludedPaths !== []) {
            Assert::allString($excludedPaths);
            // exclude paths, as notPath() does not work with absolute paths
            $finder->filter(static function (SplFileInfo $splFileInfo) use($excludedPaths) : bool {
                $realPath = $splFileInfo->getRealPath();
                foreach ($excludedPaths as $excludedPath) {
                    if (\strpos($realPath, $excludedPath) !== \false) {
                        return \false;
                    }
                    if (\strpos($excludedPath, '*') !== \false && \fnmatch($excludedPath, $realPath)) {
                        return \false;
                    }
                }
                return \true;
            });
        }
        return \iterator_to_array($finder->getIterator());
    }
    /**
     * @param string[] $directories
     * @return SplFileInfo[]
     */
    public static function findTwigFiles(array $directories) : array
    {
        Assert::allString($directories);
        Assert::allDirectory($directories);
        $twigFinder = Finder::create()->files()->name('*.twig')->in($directories)->sortByName();
        return \iterator_to_array($twigFinder->getIterator());
    }
    /**
     * @param string[] $sources
     * @return SplFileInfo[]
     */
    public static function findJsonFiles(array $sources) : array
    {
        $jsonFileInfos = [];
        $directories = [];
        foreach ($sources as $source) {
            if (\is_file($source)) {
                $jsonFileInfos[] = new SplFileInfo($source, '', $source);
            } else {
                $directories[] = $source;
            }
        }
        $jsonFileFinder = Finder::create()->files()->in($directories)->name('*.json')->sortByName();
        foreach ($jsonFileFinder->getIterator() as $fileInfo) {
            $jsonFileInfos[] = $fileInfo;
        }
        return $jsonFileInfos;
    }
    /**
     * @param string[] $paths
     * @return SplFileInfo[]
     */
    public static function findYamlFiles(array $paths) : array
    {
        Assert::allString($paths);
        Assert::allFileExists($paths);
        $finder = Finder::create()->files()->in($paths)->name('*.yml')->name('*.yaml');
        return \iterator_to_array($finder);
    }
}

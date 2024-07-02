<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Finder;

use SwissKnife202407\Symfony\Component\Finder\Finder;
use SwissKnife202407\Symfony\Component\Finder\SplFileInfo;
final class FilesFinder
{
    /**
     * @param string[] $sources
     * @return SplFileInfo[]
     */
    public static function find(array $sources) : array
    {
        $paths = [];
        foreach ($sources as $source) {
            $paths[] = \getcwd() . \DIRECTORY_SEPARATOR . $source;
        }
        $finder = Finder::create()->files()->in($paths)->sortByName();
        return \iterator_to_array($finder->getIterator());
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
}

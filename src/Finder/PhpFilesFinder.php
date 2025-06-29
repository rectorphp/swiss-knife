<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Finder;

use SwissKnife202506\Symfony\Component\Finder\Finder;
use SwissKnife202506\Symfony\Component\Finder\SplFileInfo;
use SwissKnife202506\Webmozart\Assert\Assert;
final class PhpFilesFinder
{
    /**
     * @param string[] $paths
     * @param string[] $excludedPaths
     *
     * @return SplFileInfo[]
     */
    public static function find(array $paths, array $excludedPaths = []) : array
    {
        $finder = self::createFinderForPathsAndExcludedPaths($paths, $excludedPaths);
        return \iterator_to_array($finder->getIterator());
    }
    /**
     * @param string[] $paths
     * @param string[] $excludedPaths
     */
    private static function createFinderForPathsAndExcludedPaths(array $paths, array $excludedPaths) : Finder
    {
        Assert::allString($paths);
        Assert::allFileExists($paths);
        Assert::allString($excludedPaths);
        $excludedFileNames = [];
        foreach ($excludedPaths as $excludedPath) {
            if (\strpos($excludedPath, '*') === \false) {
                $excludedFileNames[] = $excludedPath;
            }
        }
        Assert::allFileExists($excludedFileNames);
        return Finder::create()->files()->in($paths)->name('*.php')->notPath('vendor')->notPath('var')->notPath('data-fixtures')->notPath('node_modules')->filter(static function (SplFileInfo $splFileInfo) use($excludedPaths) : bool {
            foreach ($excludedPaths as $excludedPath) {
                $realpath = $splFileInfo->getRealPath();
                if (\strpos($realpath, $excludedPath) !== \false) {
                    return \false;
                }
                if (\strpos($excludedPath, '*') !== \false && \fnmatch($excludedPath, $realpath)) {
                    return \false;
                }
            }
            return \true;
        });
    }
}

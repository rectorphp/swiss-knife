<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Finder;

use SwissKnife202408\Symfony\Component\Finder\Finder;
use SwissKnife202408\Symfony\Component\Finder\SplFileInfo;
use SwissKnife202408\Webmozart\Assert\Assert;
final class PhpFilesFinder
{
    /**
     * @param string[] $paths
     * @param string[] $excludedPaths
     * @return SplFileInfo[]
     */
    public static function find(array $paths, array $excludedPaths = []) : array
    {
        Assert::allString($paths);
        Assert::allFileExists($paths);
        Assert::allString($excludedPaths);
        Assert::allFileExists($excludedPaths);
        $finder = Finder::create()->files()->in($paths)->name('*.php')->notPath('vendor')->filter(static function (SplFileInfo $splFileInfo) use($excludedPaths) : bool {
            foreach ($excludedPaths as $excludedPath) {
                if (\strpos($splFileInfo->getRealPath(), $excludedPath) !== \false) {
                    return \false;
                }
            }
            return \true;
        });
        return \iterator_to_array($finder->getIterator());
    }
}

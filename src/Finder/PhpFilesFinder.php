<?php

declare (strict_types=1);
namespace EasyCI202402\Rector\SwissKnife\Finder;

use EasyCI202402\Symfony\Component\Finder\Finder;
use EasyCI202402\Symfony\Component\Finder\SplFileInfo;
final class PhpFilesFinder
{
    /**
     * @param string[] $paths
     * @return SplFileInfo[]
     */
    public static function findPhpFileInfos(array $paths) : array
    {
        $phpFinder = Finder::create()->files()->in($paths)->name('*.php');
        return \iterator_to_array($phpFinder);
    }
}

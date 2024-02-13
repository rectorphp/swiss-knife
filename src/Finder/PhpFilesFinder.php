<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Finder;

use SwissKnife202402\Symfony\Component\Finder\Finder;
use SwissKnife202402\Symfony\Component\Finder\SplFileInfo;
use SwissKnife202402\Webmozart\Assert\Assert;
final class PhpFilesFinder
{
    /**
     * @param string[] $paths
     * @return SplFileInfo[]
     */
    public static function find(array $paths) : array
    {
        Assert::allString($paths);
        Assert::allFileExists($paths);
        $finder = Finder::create()->files()->in($paths)->name('*.php');
        return \iterator_to_array($finder);
    }
}

<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Finder;

use SwissKnife202501\Nette\Utils\Strings;
use SwissKnife202501\Symfony\Component\Finder\Finder;
use SwissKnife202501\Symfony\Component\Finder\SplFileInfo;
use SwissKnife202501\Webmozart\Assert\Assert;
final class TraitFilesFinder
{
    /**
     * @param string[] $directories
     * @return SplFileInfo[]
     */
    public function findTraitUsages(array $directories) : array
    {
        Assert::allString($directories);
        $traitUsersFinder = Finder::create()->files()->in($directories)->name('*.php')->sortByName()->filter(function (SplFileInfo $fileInfo) : bool {
            $fileContent = $fileInfo->getContents();
            return \strpos($fileContent, '    use ') !== \false;
        });
        return \iterator_to_array($traitUsersFinder->getIterator());
    }
    /**
     * @param string[] $directories
     * @return array<SplFileInfo>
     */
    public function find(array $directories) : array
    {
        Assert::allString($directories);
        $traitFinder = Finder::create()->files()->in($directories)->name('*.php')->notPath('Entity')->notPath('Document')->sortByName()->filter(function (SplFileInfo $fileInfo) : bool {
            $fileContent = $fileInfo->getContents();
            return (bool) Strings::match($fileContent, '#^trait\\s#m');
        });
        return \iterator_to_array($traitFinder->getIterator());
    }
}

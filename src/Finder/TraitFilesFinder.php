<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Finder;

use Nette\Utils\Strings;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Webmozart\Assert\Assert;

final class TraitFilesFinder
{
    /**
     * @param string[] $directories
     * @return SplFileInfo[]
     */
    public function findTraitUsages(array $directories): array
    {
        Assert::allString($directories);

        $traitUsersFinder = Finder::create()
            ->files()
            ->in($directories)
            ->name('*.php')
            ->sortByName()
            ->filter(function (SplFileInfo $fileInfo): bool {
                $fileContent = $fileInfo->getContents();
                return str_contains($fileContent, '    use ');
            });

        return iterator_to_array($traitUsersFinder->getIterator());
    }

    /**
     * @param string[] $directories
     * @return array<SplFileInfo>
     */
    public function find(array $directories): array
    {
        Assert::allString($directories);

        $traitFinder = Finder::create()
            ->files()
            ->in($directories)
            ->name('*.php')
            ->notPath('Entity')
            ->notPath('Document')
            ->sortByName()
            ->filter(function (SplFileInfo $fileInfo): bool {
                $fileContent = $fileInfo->getContents();
                return (bool) Strings::match($fileContent, '#^trait\s#m');
            });

        return iterator_to_array($traitFinder->getIterator());
    }
}

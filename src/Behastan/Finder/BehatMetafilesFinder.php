<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Behastan\Finder;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Webmozart\Assert\Assert;

final class BehatMetafilesFinder
{
    /**
     * @param string[] $directories
     * @return SplFileInfo[]
     */
    public function findContextFiles(array $directories): array
    {
        Assert::allString($directories);
        Assert::allDirectory($directories);

        $filesFinder = Finder::create()
            ->files()
            ->name('*Context.php')
            ->in($directories);

        return iterator_to_array($filesFinder->getIterator());
    }

    /**
     * @param string[] $directories
     *
     * @return SplFileInfo[]
     */
    public function findFeatureFiles(array $directories): array
    {
        Assert::allString($directories);
        Assert::allDirectory($directories);

        $filesFinder = Finder::create()
            ->files()
            ->name('*.feature')
            ->in($directories);

        return iterator_to_array($filesFinder->getIterator());
    }
}

<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Finder;

use Symplify\EasyCI\RobotLoader\PhpClassLoader;

final readonly class MultipleClassInOneFileFinder
{
    public function __construct(
        private PhpClassLoader $phpClassLoader
    ) {
    }

    /**
     * @param string[] $directories
     * @return string[][]
     */
    public function findInDirectories(array $directories): array
    {
        $fileByClasses = $this->phpClassLoader->load($directories);

        $classesByFile = [];
        foreach ($fileByClasses as $class => $file) {
            $classesByFile[$file][] = $class;
        }

        return array_filter($classesByFile, static fn (array $classes): bool => count($classes) >= 2);
    }
}

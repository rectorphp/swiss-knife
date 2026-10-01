<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Testing\Finder;

use Entropy\Reflection\ClassNameResolver;
use Rector\SwissKnife\Finder\PhpFilesFinder;

final class TestCaseClassFinder
{
    /**
     * @param string[] $directories
     * @return array<string, string>
     */
    public function findInDirectories(array $directories): array
    {
        $classesToFilePaths = [];

        foreach (PhpFilesFinder::find($directories) as $fileInfo) {
            $filePath = (string) $fileInfo->getRealPath();

            foreach (ClassNameResolver::resolveNamesFromFilePath($filePath) as $className) {
                $classesToFilePaths[$className] = $filePath;
            }
        }

        $this->includeNonAutoloadedClasses($classesToFilePaths);

        return $classesToFilePaths;
    }

    /**
     * @param array<string, string> $classesToFilePaths
     */
    private function includeNonAutoloadedClasses(array $classesToFilePaths): void
    {
        foreach ($classesToFilePaths as $class => $filePath) {
            if (class_exists($class)) {
                continue;
            }

            if (interface_exists($class)) {
                continue;
            }

            if (trait_exists($class)) {
                continue;
            }

            require_once $filePath;
        }
    }
}

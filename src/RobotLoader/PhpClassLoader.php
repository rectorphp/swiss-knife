<?php

declare(strict_types=1);

namespace Rector\SwissKnife\RobotLoader;

use Entropy\Reflection\ClassNameResolver;
use Rector\SwissKnife\Finder\PhpFilesFinder;

final class PhpClassLoader
{
    /**
     * @param string[] $directories
     * @param string[] $excludedPaths
     * @return array<string, string>
     */
    public function load(array $directories, array $excludedPaths): array
    {
        $classesToFilePaths = [];

        foreach (PhpFilesFinder::find($directories, $excludedPaths) as $fileInfo) {
            $filePath = (string) $fileInfo->getRealPath();

            foreach (ClassNameResolver::resolveNamesFromFilePath($filePath) as $className) {
                $classesToFilePaths[$className] = $filePath;
            }
        }

        return $classesToFilePaths;
    }
}

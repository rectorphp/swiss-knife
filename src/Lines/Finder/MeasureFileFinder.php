<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Lines\Finder;

use Entropy\FileSystem\FileFinder;
use Entropy\FileSystem\FileInfo;
use Entropy\Validation\Assert;

final class MeasureFileFinder
{
    /**
     * @param string[] $paths
     * @param string[] $excludes
     *
     * @return string[]
     */
    public static function find(array $paths, array $excludes = [], bool $allowVendor = false): array
    {
        Assert::allString($paths);
        Assert::allFileExists($paths);

        $filePaths = [];
        $directories = [];

        foreach ($paths as $path) {
            if (is_file($path)) {
                $filePaths[] = $path;
            } elseif (is_dir($path)) {
                $directories[] = $path;
            }
        }

        $fileInfos = FileFinder::find(
            $directories,
            static function (FileInfo $fileInfo) use ($excludes, $allowVendor): bool {
                if ($fileInfo->getExtension() !== 'php') {
                    return false;
                }

                $normalizedRelativePath = '/' . str_replace('\\', '/', $fileInfo->getRelativePathname());

                // symfony cache dir
                if (strpos($normalizedRelativePath, '/var/') !== false) {
                    return false;
                }

                if (! $allowVendor && strpos($normalizedRelativePath, '/vendor/') !== false) {
                    return false;
                }

                $realPath = (string) $fileInfo->getRealPath();
                foreach ($excludes as $exclude) {
                    if (strpos($realPath, $exclude) !== false) {
                        return false;
                    }
                }

                return true;
            }
        );

        foreach ($fileInfos as $fileInfo) {
            $filePaths[] = (string) $fileInfo->getRealPath();
        }

        return $filePaths;
    }
}

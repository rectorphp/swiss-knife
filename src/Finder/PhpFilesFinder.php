<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Finder;

use Entropy\FileSystem\FileFinder;
use Entropy\FileSystem\FileInfo;
use Entropy\Validation\Assert;

/**
 * @see \Rector\SwissKnife\Tests\Finder\PhpFilesFinderTest
 */
final class PhpFilesFinder
{
    /**
     * @var string[]
     */
    private const SKIPPED_DIRECTORIES = ['vendor', 'var', 'data-fixtures', 'node_modules'];

    /**
     * @param string[] $paths
     * @param string[] $excludedPaths
     *
     * @return FileInfo[]
     */
    public static function find(array $paths, array $excludedPaths = []): array
    {
        Assert::allString($paths);
        Assert::allFileExists($paths);
        Assert::allString($excludedPaths);

        $excludedFileNames = [];
        foreach ($excludedPaths as $excludedPath) {
            if (strpos($excludedPath, '*') === false) {
                $excludedFileNames[] = $excludedPath;
            }
        }

        Assert::allFileExists($excludedFileNames);

        return FileFinder::find($paths, static function (FileInfo $fileInfo) use ($excludedPaths): bool {
            if ($fileInfo->getExtension() !== 'php') {
                return false;
            }

            $normalizedRelativePath = '/' . str_replace('\\', '/', $fileInfo->getRelativePathname());
            foreach (self::SKIPPED_DIRECTORIES as $skippedDirectory) {
                if (strpos($normalizedRelativePath, '/' . $skippedDirectory . '/') !== false) {
                    return false;
                }
            }

            $realPath = (string) $fileInfo->getRealPath();

            foreach ($excludedPaths as $excludedPath) {
                if (strpos($realPath, $excludedPath) !== false) {
                    return false;
                }

                if (strpos($excludedPath, '*') !== false && fnmatch($excludedPath, $realPath)) {
                    return false;
                }
            }

            return true;
        });
    }
}

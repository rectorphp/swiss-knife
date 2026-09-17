<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Finder;

use Entropy\FileSystem\FileFinder;
use Entropy\FileSystem\FileInfo;
use Webmozart\Assert\Assert;

/**
 * @see \Rector\SwissKnife\Tests\Finder\PhpFilesFinderTest
 */
final class PhpFilesFinder
{
    /**
     * @var string[]
     */
    private const array SKIPPED_DIRECTORIES = ['vendor', 'var', 'data-fixtures', 'node_modules'];

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
            if (! str_contains($excludedPath, '*')) {
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
                if (str_contains($normalizedRelativePath, '/' . $skippedDirectory . '/')) {
                    return false;
                }
            }

            $realPath = (string) $fileInfo->getRealPath();

            foreach ($excludedPaths as $excludedPath) {
                if (str_contains($realPath, $excludedPath)) {
                    return false;
                }

                if (str_contains($excludedPath, '*') && fnmatch($excludedPath, $realPath)) {
                    return false;
                }
            }

            return true;
        });
    }
}

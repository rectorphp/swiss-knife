<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Finder;

use Entropy\FileSystem\FileFinder;
use Entropy\FileSystem\FileInfo;
use Entropy\Validation\Assert;

/**
 * @see \Rector\SwissKnife\Tests\Finder\FilesFinderTest
 */
final class FilesFinder
{
    /**
     * @var string[]
     */
    private const SKIPPED_DIRECTORIES = ['node_modules', 'vendor', 'var/cache'];

    /**
     * @param string[] $sources
     * @param string[] $excludedPaths
     * @return FileInfo[]
     */
    public static function find(array $sources, array $excludedPaths = []): array
    {
        Assert::allString($excludedPaths);

        $directories = [];
        foreach ($sources as $source) {
            $directories[] = getcwd() . DIRECTORY_SEPARATOR . $source;
        }

        return FileFinder::find($directories, static function (FileInfo $fileInfo) use ($excludedPaths): bool {
            // not our code
            $normalizedRelativePath = '/' . str_replace('\\', '/', $fileInfo->getRelativePathname());
            foreach (self::SKIPPED_DIRECTORIES as $skippedDirectory) {
                if (strpos($normalizedRelativePath, '/' . $skippedDirectory . '/') !== false) {
                    return false;
                }
            }

            return ! self::isExcluded((string) $fileInfo->getRealPath(), $excludedPaths);
        });
    }

    /**
     * @param string[] $directories
     * @return FileInfo[]
     */
    public static function findTwigFiles(array $directories): array
    {
        Assert::allString($directories);
        Assert::allDirectory($directories);

        return FileFinder::find(
            $directories,
            static fn (FileInfo $fileInfo): bool => $fileInfo->getExtension() === 'twig'
        );
    }

    /**
     * @param string[] $paths
     * @return FileInfo[]
     */
    public static function findYamlFiles(array $paths): array
    {
        Assert::allString($paths);
        Assert::allFileExists($paths);

        return FileFinder::find(
            $paths,
            static fn (FileInfo $fileInfo): bool => in_array($fileInfo->getExtension(), ['yml', 'yaml'], true)
        );
    }

    /**
     * @param string[] $excludedPaths
     */
    private static function isExcluded(string $realPath, array $excludedPaths): bool
    {
        foreach ($excludedPaths as $excludedPath) {
            if (strpos($realPath, $excludedPath) !== false) {
                return true;
            }

            if (strpos($excludedPath, '*') !== false && fnmatch($excludedPath, $realPath)) {
                return true;
            }
        }

        return false;
    }
}

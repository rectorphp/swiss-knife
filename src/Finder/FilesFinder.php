<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Finder;

use Rector\SwissKnife\ValueObject\FileInfo;
use Webmozart\Assert\Assert;

/**
 * @see \Rector\SwissKnife\Tests\Finder\FilesFinderTest
 */
final class FilesFinder
{
    /**
     * @var string[]
     */
    private const array SKIPPED_DIRECTORIES = ['node_modules', 'vendor', 'var/cache'];

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

        return FileScanner::scan($directories, static function (FileInfo $fileInfo) use ($excludedPaths): bool {
            // not our code
            $normalizedRelativePath = '/' . str_replace('\\', '/', $fileInfo->getRelativePathname());
            foreach (self::SKIPPED_DIRECTORIES as $skippedDirectory) {
                if (str_contains($normalizedRelativePath, '/' . $skippedDirectory . '/')) {
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

        return FileScanner::scan(
            $directories,
            static fn (FileInfo $fileInfo): bool => $fileInfo->getExtension() === 'twig'
        );
    }

    /**
     * @param string[] $sources
     * @return FileInfo[]
     */
    public static function findJsonFiles(array $sources): array
    {
        $jsonFileInfos = [];
        $directories = [];

        foreach ($sources as $source) {
            if (is_file($source)) {
                $jsonFileInfos[] = new FileInfo($source, '', $source);
            } else {
                $directories[] = $source;
            }
        }

        $scannedFileInfos = FileScanner::scan(
            $directories,
            static fn (FileInfo $fileInfo): bool => $fileInfo->getExtension() === 'json'
        );

        return array_merge($jsonFileInfos, $scannedFileInfos);
    }

    /**
     * @param string[] $paths
     * @return FileInfo[]
     */
    public static function findYamlFiles(array $paths): array
    {
        Assert::allString($paths);
        Assert::allFileExists($paths);

        return FileScanner::scan(
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
            if (str_contains($realPath, $excludedPath)) {
                return true;
            }

            if (str_contains($excludedPath, '*') && fnmatch($excludedPath, $realPath)) {
                return true;
            }
        }

        return false;
    }
}

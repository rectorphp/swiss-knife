<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Finder;

use Rector\SwissKnife\ValueObject\FileInfo;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final class FileScanner
{
    /**
     * @param string[] $directories
     * @param callable(FileInfo): bool $filter
     * @return FileInfo[]
     */
    public static function scan(array $directories, callable $filter): array
    {
        $fileInfos = [];

        foreach ($directories as $directory) {
            if (! is_dir($directory)) {
                continue;
            }

            $baseDirectory = str_replace('\\', '/', (string) realpath($directory));

            $recursiveDirectoryIterator = new RecursiveDirectoryIterator(
                $directory,
                RecursiveDirectoryIterator::SKIP_DOTS
            );
            $recursiveIteratorIterator = new RecursiveIteratorIterator($recursiveDirectoryIterator);

            foreach ($recursiveIteratorIterator as $splFileInfo) {
                if (! $splFileInfo instanceof SplFileInfo) {
                    continue;
                }

                if (! $splFileInfo->isFile()) {
                    continue;
                }

                $filePath = (string) $splFileInfo->getRealPath();
                $relativePathname = self::resolveRelativePathname($filePath, $baseDirectory);
                $relativeDirectory = dirname($relativePathname);

                $fileInfo = new FileInfo(
                    $filePath,
                    $relativeDirectory === '.' ? '' : $relativeDirectory,
                    $relativePathname
                );

                if (! $filter($fileInfo)) {
                    continue;
                }

                $fileInfos[$filePath] = $fileInfo;
            }
        }

        ksort($fileInfos);

        return array_values($fileInfos);
    }

    private static function resolveRelativePathname(string $filePath, string $baseDirectory): string
    {
        $filePath = str_replace('\\', '/', $filePath);

        if ($baseDirectory !== '' && str_starts_with($filePath, $baseDirectory . '/')) {
            return substr($filePath, strlen($baseDirectory) + 1);
        }

        return $filePath;
    }
}

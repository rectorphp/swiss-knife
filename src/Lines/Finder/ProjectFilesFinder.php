<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Lines\Finder;

use Entropy\FileSystem\FileFinder;
use Entropy\FileSystem\FileInfo;

final class ProjectFilesFinder
{
    /**
     * @var string[]
     */
    private const SKIPPED_DIRECTORIES = ['vendor', 'stubs', 'bin', 'migrations', 'data-fixtures', 'build'];

    /**
     * @return FileInfo[]
     */
    public static function find(string $projectDirectory): array
    {
        return FileFinder::find([$projectDirectory], static function (FileInfo $fileInfo): bool {
            if ($fileInfo->getExtension() !== 'php') {
                return false;
            }

            $normalizedRelativePath = '/' . str_replace('\\', '/', $fileInfo->getRelativePathname());
            foreach (self::SKIPPED_DIRECTORIES as $skippedDirectory) {
                if (strpos($normalizedRelativePath, '/' . $skippedDirectory . '/') !== false) {
                    return false;
                }
            }

            return true;
        });
    }
}

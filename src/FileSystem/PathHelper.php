<?php

declare(strict_types=1);

namespace TomasVotruba\Lemonade\FileSystem;

final class PathHelper
{
    public static function relativeToDirectory(string $filePath, string $directory): string
    {
        $filePath = self::normalize($filePath);

        // get relative path from getcwd()
        return str_replace(self::normalize($directory) . '/', '', $filePath);
    }

    public static function relativeToCwd(string $filePath): string
    {
        return self::relativeToDirectory($filePath, getcwd());
    }

    private static function normalize(string $filePath): string
    {
        return str_replace('\\', '/', $filePath);
    }
}

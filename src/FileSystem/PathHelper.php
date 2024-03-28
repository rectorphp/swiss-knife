<?php

declare(strict_types=1);

namespace TomasVotruba\Lemonade\FileSystem;

final class PathHelper
{
    public static function relativeToCwd(string $filePath): string
    {
        $filePath = self::normalize($filePath);

        // get relative path from getcwd()
        return str_replace(self::normalize((string) getcwd()) . '/', '', $filePath);
    }

    private static function normalize(string $filePath): string
    {
        return str_replace('\\', '/', $filePath);
    }
}

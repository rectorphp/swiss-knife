<?php

declare (strict_types=1);
namespace Rector\SwissKnife\FileSystem;

final class PathHelper
{
    public static function relativeToCwd(string $filePath) : string
    {
        // get relative path from getcwd()
        return \str_replace(\getcwd() . '/', '', $filePath);
    }
}

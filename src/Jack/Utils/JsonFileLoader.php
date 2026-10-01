<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Jack\Utils;

use Entropy\Utils\FileSystem;
use Entropy\Utils\Json;
use Entropy\Validation\Assert;

final class JsonFileLoader
{
    /**
     * @return array<string, mixed>
     */
    public static function loadFileToJson(string $filePath): array
    {
        Assert::fileExists($filePath);

        $fileContents = FileSystem::read($filePath);

        return Json::decode($fileContents);
    }
}

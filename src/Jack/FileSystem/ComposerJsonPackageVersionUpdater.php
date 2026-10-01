<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Jack\FileSystem;

use Entropy\Utils\Regex;

final class ComposerJsonPackageVersionUpdater
{
    public static function update(string $composerJsonContents, string $packageName, string $newVersion): string
    {
        // replace using regex, to keep original composer.json format
        $allChanges = Regex::replace(
            $composerJsonContents,
            // find
            sprintf('#"%s": "(.*?)"#', $packageName),
            // replace
            sprintf('"%s": "%s"', $packageName, $newVersion)
        );

        $skippedKeys = ['suggest', 'replace', 'provide', 'conflict'];

        foreach ($skippedKeys as $skippedKey) {
            $regexKeyContent = sprintf('#"%s"\s*:\s*{[^}]*}#', $skippedKey);
            $skippedContent = Regex::match($composerJsonContents, $regexKeyContent);

            if (isset($skippedContent[0]) && is_string($skippedContent[0])) {
                $allChanges = Regex::replace($allChanges, $regexKeyContent, $skippedContent[0]);
            }
        }

        return $allChanges;
    }
}

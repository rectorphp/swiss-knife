<?php

declare(strict_types=1);

namespace Rector\SwissKnife\SmokeTestgen\FileSystem;

final class TestsDirectoryResolver
{
    public function resolveSmokeUnitTestDirectory(string $projectDirectory): string
    {
        $testDirectory = $this->resolveTestDirectory($projectDirectory);

        // fallback to default
        $testDirectory ??= 'tests';

        $unitTestDirectory = $this->resolveUnitTestsDirectory($testDirectory);
        if ($unitTestDirectory === null) {
            // fallback to "Unit"
            return $testDirectory . '/Unit/Smoke';

        }

        return $testDirectory . '/' . $unitTestDirectory . '/Smoke';
    }

    private function resolveUnitTestsDirectory(string $testDirectory): ?string
    {
        return $this->findFirstDirectoryByNameRegex($testDirectory, '#unit#i');
    }

    private function resolveTestDirectory(string $projectDirectory): ?string
    {
        return $this->findFirstDirectoryByNameRegex($projectDirectory, '#test#i');
    }

    private function findFirstDirectoryByNameRegex(string $directory, string $nameRegex): ?string
    {
        if (! is_dir($directory)) {
            return null;
        }

        $childDirectories = glob($directory . '/*', GLOB_ONLYDIR);
        if ($childDirectories === false) {
            return null;
        }

        sort($childDirectories);

        foreach ($childDirectories as $childDirectory) {
            $directoryName = basename($childDirectory);
            if (preg_match($nameRegex, $directoryName) === 1) {
                return $directoryName;
            }
        }

        return null;
    }
}

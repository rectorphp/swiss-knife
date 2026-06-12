<?php

declare (strict_types=1);
namespace Rector\SwissKnife\SmokeTestgen\FileSystem;

use SwissKnife202606\Symfony\Component\Finder\Finder;
final class TestsDirectoryResolver
{
    public function resolveSmokeUnitTestDirectory(string $projectDirectory) : string
    {
        $testDirectory = $this->resolveTestDirectory($projectDirectory);
        if ($testDirectory === null) {
            // fallback to default
            $testDirectory = 'tests';
        }
        $unitTestDirectory = $this->resolveUnitTestsDirectory($testDirectory);
        if ($unitTestDirectory === null) {
            // fallback to "Unit"
            return $testDirectory . '/Unit/Smoke';
        }
        return $testDirectory . '/' . $unitTestDirectory . '/Smoke';
    }
    private function resolveUnitTestsDirectory(string $testDirectory) : ?string
    {
        // find test directory
        $iterator = Finder::create()->directories()->name('#unit#i')->in($testDirectory)->depth(0)->getIterator();
        foreach ($iterator as $unitTestDirectory) {
            return $unitTestDirectory->getRelativePathname();
        }
        return null;
    }
    private function resolveTestDirectory(string $projectDirectory) : ?string
    {
        // find test directory
        $iterator = Finder::create()->directories()->name('#test#i')->in($projectDirectory)->depth(0)->getIterator();
        foreach ($iterator as $testDirectory) {
            return $testDirectory->getRelativePathname();
        }
        return null;
    }
}

<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Testing\Finder;

use SwissKnife202412\Nette\Loaders\RobotLoader;
final class TestCaseClassFinder
{
    /**
     * @param string[] $directories
     * @return array<string, string>
     */
    public function findInDirectories(array $directories) : array
    {
        $robotLoader = new RobotLoader();
        $robotLoader->addDirectory(...$directories);
        $robotLoader->rebuild();
        $this->includeNonAutoloadedClasses($robotLoader->getIndexedClasses());
        return $robotLoader->getIndexedClasses();
    }
    /**
     * @param array<string, string> $classesToFilePaths
     */
    private function includeNonAutoloadedClasses(array $classesToFilePaths) : void
    {
        foreach ($classesToFilePaths as $class => $filePath) {
            if (\class_exists($class)) {
                continue;
            }
            if (\interface_exists($class)) {
                continue;
            }
            if (\trait_exists($class)) {
                continue;
            }
            require_once $filePath;
        }
    }
}

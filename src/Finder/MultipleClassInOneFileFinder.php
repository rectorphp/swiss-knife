<?php

declare (strict_types=1);
namespace EasyCI202402\Rector\SwissKnife\Finder;

use EasyCI202402\Rector\SwissKnife\RobotLoader\PhpClassLoader;
final class MultipleClassInOneFileFinder
{
    /**
     * @readonly
     * @var \Rector\SwissKnife\RobotLoader\PhpClassLoader
     */
    private $phpClassLoader;
    public function __construct(PhpClassLoader $phpClassLoader)
    {
        $this->phpClassLoader = $phpClassLoader;
    }
    /**
     * @param string[] $directories
     * @return array<string, string[]>
     */
    public function findInDirectories(array $directories) : array
    {
        $fileByClasses = $this->phpClassLoader->load($directories);
        $classesByFile = [];
        foreach ($fileByClasses as $class => $filePath) {
            $classesByFile[$filePath][] = $class;
        }
        return \array_filter($classesByFile, static function (array $classes) : bool {
            return \count($classes) >= 2;
        });
    }
}

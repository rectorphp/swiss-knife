<?php

namespace EasyCI202402\Rector\SwissKnife\RobotLoader;

use EasyCI202402\Nette\Loaders\RobotLoader;
final class PhpClassLoader
{
    /**
     * @param string[] $directories
     * @return array<string, string>
     */
    public function load(array $directories) : array
    {
        $robotLoader = new RobotLoader();
        $robotLoader->addDirectory(...$directories);
        $robotLoader->setTempDirectory(\sys_get_temp_dir() . '/multiple-classes');
        $robotLoader->rebuild();
        return $robotLoader->getIndexedClasses();
    }
}

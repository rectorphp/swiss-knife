<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Psr4\Finder;

use EasyCI202301\Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCI\Psr4\RobotLoader\PhpClassLoader;
final class MultipleClassInOneFileFinder
{
    /**
     * @var \Symplify\EasyCI\Psr4\RobotLoader\PhpClassLoader
     */
    private $phpClassLoader;
    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    public function __construct(PhpClassLoader $phpClassLoader, SymfonyStyle $symfonyStyle)
    {
        $this->phpClassLoader = $phpClassLoader;
        $this->symfonyStyle = $symfonyStyle;
    }
    /**
     * @param string[] $directories
     * @return string[][]
     */
    public function findInDirectories(array $directories) : array
    {
        $fileByClasses = $this->phpClassLoader->load($directories);
        $message = \sprintf('Analyzing %d PHP files', \count($fileByClasses));
        $this->symfonyStyle->note($message);
        $classesByFile = [];
        foreach ($fileByClasses as $class => $file) {
            $classesByFile[$file][] = $class;
        }
        return \array_filter($classesByFile, static function (array $classes) : bool {
            return \count($classes) >= 2;
        });
    }
}

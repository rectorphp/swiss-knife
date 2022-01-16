<?php

declare (strict_types=1);
namespace EasyCI20220116\Symplify\EasyCI\Config\ConfigFileAnalyzer;

use EasyCI20220116\Symplify\EasyCI\Config\ClassExtractor;
use EasyCI20220116\Symplify\EasyCI\Config\Contract\ConfigFileAnalyzerInterface;
use EasyCI20220116\Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use EasyCI20220116\Symplify\EasyCI\ValueObject\FileError;
use EasyCI20220116\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use EasyCI20220116\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\EasyCI\Tests\Config\ConfigFileAnalyzer\NonExistingClassConfigFileAnalyzer\NonExistingClassConfigFileAnalyzerTest
 */
final class NonExistingClassConfigFileAnalyzer implements \EasyCI20220116\Symplify\EasyCI\Config\Contract\ConfigFileAnalyzerInterface
{
    /**
     * @var \Symplify\EasyCI\Config\ClassExtractor
     */
    private $classExtractor;
    /**
     * @var \Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker
     */
    private $classLikeExistenceChecker;
    public function __construct(\EasyCI20220116\Symplify\EasyCI\Config\ClassExtractor $classExtractor, \EasyCI20220116\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker $classLikeExistenceChecker)
    {
        $this->classExtractor = $classExtractor;
        $this->classLikeExistenceChecker = $classLikeExistenceChecker;
    }
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return FileErrorInterface[]
     */
    public function processFileInfos(array $fileInfos) : array
    {
        $fileErrors = [];
        foreach ($fileInfos as $fileInfo) {
            $nonExistingClasses = $this->extractFromFileInfo($fileInfo);
            foreach ($nonExistingClasses as $nonExistingClass) {
                $errorMessage = \sprintf('Class "%s" not found', $nonExistingClass);
                $fileErrors[] = new \EasyCI20220116\Symplify\EasyCI\ValueObject\FileError($errorMessage, $fileInfo);
            }
        }
        return $fileErrors;
    }
    /**
     * @return string[]
     */
    private function extractFromFileInfo(\EasyCI20220116\Symplify\SmartFileSystem\SmartFileInfo $fileInfo) : array
    {
        $classes = $this->classExtractor->extractFromFileInfo($fileInfo);
        $nonExistingClasses = $this->filterNonExistingClasses($classes);
        if ($nonExistingClasses === []) {
            return [];
        }
        \sort($nonExistingClasses);
        return $nonExistingClasses;
    }
    /**
     * @param string[] $classes
     * @return string[]
     */
    private function filterNonExistingClasses(array $classes) : array
    {
        return \array_filter($classes, function (string $class) : bool {
            return !$this->classLikeExistenceChecker->doesClassLikeExist($class);
        });
    }
}

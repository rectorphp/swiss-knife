<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Config\ConfigFileAnalyzer;

use Symplify\EasyCI\Config\ClassExtractor;
use Symplify\EasyCI\Config\Contract\ConfigFileAnalyzerInterface;
use Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use Symplify\EasyCI\ValueObject\FileError;
use EasyCI202301\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use EasyCI202301\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\EasyCI\Tests\Config\ConfigFileAnalyzer\NonExistingClassConfigFileAnalyzer\NonExistingClassConfigFileAnalyzerTest
 */
final class NonExistingClassConfigFileAnalyzer implements ConfigFileAnalyzerInterface
{
    /**
     * @var \Symplify\EasyCI\Config\ClassExtractor
     */
    private $classExtractor;
    /**
     * @var \Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker
     */
    private $classLikeExistenceChecker;
    public function __construct(ClassExtractor $classExtractor, ClassLikeExistenceChecker $classLikeExistenceChecker)
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
                $fileErrors[] = new FileError($errorMessage, $fileInfo);
            }
        }
        return $fileErrors;
    }
    /**
     * @return string[]
     */
    private function extractFromFileInfo(SmartFileInfo $fileInfo) : array
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

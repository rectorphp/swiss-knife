<?php

declare (strict_types=1);
namespace EasyCI20220607\Symplify\EasyCI\Testing;

use EasyCI20220607\Symplify\EasyCI\Testing\Autoloading\DualTestCaseAuloader;
use EasyCI20220607\Symplify\EasyCI\Testing\Finder\TestCaseClassFinder;
/**
 * @see \Symplify\EasyCI\Tests\Testing\UnitTestFilePathsFinder\UnitTestFilePathsFinderTest
 */
final class UnitTestFilePathsFinder
{
    /**
     * @var \Symplify\EasyCI\Testing\Autoloading\DualTestCaseAuloader
     */
    private $dualTestCaseAuloader;
    /**
     * @var \Symplify\EasyCI\Testing\Finder\TestCaseClassFinder
     */
    private $testCaseClassFinder;
    /**
     * @var \Symplify\EasyCI\Testing\UnitTestFilter
     */
    private $unitTestFilter;
    public function __construct(DualTestCaseAuloader $dualTestCaseAuloader, TestCaseClassFinder $testCaseClassFinder, UnitTestFilter $unitTestFilter)
    {
        $this->dualTestCaseAuloader = $dualTestCaseAuloader;
        $this->testCaseClassFinder = $testCaseClassFinder;
        $this->unitTestFilter = $unitTestFilter;
    }
    /**
     * @param string[] $directories
     * @return array<string, string>
     */
    public function findInDirectories(array $directories) : array
    {
        $this->dualTestCaseAuloader->autoload();
        $testsCasesClassesToFilePaths = $this->testCaseClassFinder->findInDirectories($directories);
        return $this->unitTestFilter->filter($testsCasesClassesToFilePaths);
    }
}

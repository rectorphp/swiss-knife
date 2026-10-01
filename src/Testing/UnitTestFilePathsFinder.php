<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Testing;

use Rector\SwissKnife\Testing\Finder\TestCaseClassFinder;

/**
 * @see \Rector\SwissKnife\Tests\Testing\UnitTestFilePathsFinder\UnitTestFilePathsFinderTest
 */
final class UnitTestFilePathsFinder
{
    /**
     * @readonly
     */
    private TestCaseClassFinder $testCaseClassFinder;

    /**
     * @readonly
     */
    private UnitTestFilter $unitTestFilter;

    public function __construct(TestCaseClassFinder $testCaseClassFinder, UnitTestFilter $unitTestFilter)
    {
        $this->testCaseClassFinder = $testCaseClassFinder;
        $this->unitTestFilter = $unitTestFilter;
    }

    /**
     * @param string[] $directories
     * @return array<string, string>
     */
    public function findInDirectories(array $directories): array
    {
        $testsCasesClassesToFilePaths = $this->testCaseClassFinder->findInDirectories($directories);

        return $this->unitTestFilter->filter($testsCasesClassesToFilePaths);
    }
}

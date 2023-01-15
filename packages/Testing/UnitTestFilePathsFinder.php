<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Testing;

use Symplify\EasyCI\Testing\Finder\TestCaseClassFinder;

/**
 * @see \Symplify\EasyCI\Tests\Testing\UnitTestFilePathsFinder\UnitTestFilePathsFinderTest
 */
final class UnitTestFilePathsFinder
{
    public function __construct(
        private readonly TestCaseClassFinder $testCaseClassFinder,
        private readonly UnitTestFilter $unitTestFilter,
    ) {
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

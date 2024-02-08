<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Testing;

use Rector\SwissKnife\Testing\Finder\TestCaseClassFinder;

/**
 * @see \Rector\SwissKnife\Tests\Testing\UnitTestFilePathsFinder\UnitTestFilePathsFinderTest
 */
final readonly class UnitTestFilePathsFinder
{
    public function __construct(
        private TestCaseClassFinder $testCaseClassFinder,
        private UnitTestFilter $unitTestFilter,
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

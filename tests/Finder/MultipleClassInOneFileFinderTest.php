<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Finder;

use Rector\SwissKnife\Finder\MultipleClassInOneFileFinder;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class MultipleClassInOneFileFinderTest extends AbstractTestCase
{
    public function testFindInDirectories(): void
    {
        $multipleClassInOneFileFinder = $this->make(MultipleClassInOneFileFinder::class);

        $multipleClassesByFile = $multipleClassInOneFileFinder->findInDirectories(
            [__DIR__ . '/MultiClassFixture'],
            []
        );

        $twoClassesFilePath = __DIR__ . '/MultiClassFixture/TwoClassesInOneFile.php';
        $this->assertArrayHasKey($twoClassesFilePath, $multipleClassesByFile);
        $this->assertCount(2, $multipleClassesByFile[$twoClassesFilePath]);
    }
}

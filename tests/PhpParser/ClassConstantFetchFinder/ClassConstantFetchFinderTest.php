<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\PhpParser\ClassConstantFetchFinder;

use Rector\SwissKnife\Contract\ClassConstantFetchInterface;
use Rector\SwissKnife\Finder\PhpFilesFinder;
use Rector\SwissKnife\PhpParser\ClassConstantFetchFinder;
use Rector\SwissKnife\Tests\AbstractTestCase;
use Rector\SwissKnife\ValueObject\ClassConstantFetch\CurrentClassConstantFetch;
use Rector\SwissKnife\ValueObject\ClassConstantFetch\ExternalClassAccessConstantFetch;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\NullOutput;

final class ClassConstantFetchFinderTest extends AbstractTestCase
{
    private ClassConstantFetchFinder $classConstantsFetchFinder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->classConstantsFetchFinder = $this->make(ClassConstantFetchFinder::class);
    }

    public function testSkipInterfaceTraitAndEnum(): void
    {
        $classConstantFetches = $this->findInDirectory(__DIR__ . '/Fixture/Skip');
        $this->assertCount(0, $classConstantFetches);
    }

    public function test(): void
    {
        $classConstantFetches = $this->findInDirectory(__DIR__ . '/Fixture/Standard');
        $this->assertCount(2, $classConstantFetches);

        $firstClassConstantFetch = $classConstantFetches[0];
        $this->assertInstanceOf(CurrentClassConstantFetch::class, $firstClassConstantFetch);

        $secondClassConstantFetch = $classConstantFetches[1];
        $this->assertInstanceOf(ExternalClassAccessConstantFetch::class, $secondClassConstantFetch);
    }

    /**
     * @return ClassConstantFetchInterface[]
     */
    private function findInDirectory(string $directory): array
    {
        $progressBar = new ProgressBar(new NullOutput());
        $fileInfos = PhpFilesFinder::find([$directory]);

        return $this->classConstantsFetchFinder->find($fileInfos, $progressBar, false);
    }
}
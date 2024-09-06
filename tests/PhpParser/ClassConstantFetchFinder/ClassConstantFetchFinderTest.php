<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\PhpParser\ClassConstantFetchFinder;

use Rector\SwissKnife\Finder\PhpFilesFinder;
use Rector\SwissKnife\PhpParser\ClassConstantFetchFinder;
use Rector\SwissKnife\Tests\AbstractTestCase;
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

    public function test(): void
    {
        $progressBar = new ProgressBar(new NullOutput());

        $fileInfos = PhpFilesFinder::find([__DIR__ . '/Fixture']);

        $classConstantFetches = $this->classConstantsFetchFinder->find($fileInfos, $progressBar);
        $this->assertCount(1, $classConstantFetches);

        $firstClassConstantFetch = $classConstantFetches[0];
        $this->assertInstanceOf(ExternalClassAccessConstantFetch::class, $firstClassConstantFetch);
    }
}

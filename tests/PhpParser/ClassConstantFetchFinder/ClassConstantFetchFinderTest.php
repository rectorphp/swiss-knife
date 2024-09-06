<?php

declare(strict_types=1);

namespace PhpParser\ClassConstantFetchFinder;

use Rector\SwissKnife\Finder\PhpFilesFinder;
use Rector\SwissKnife\PhpParser\ClassConstantFetchFinder;
use Rector\SwissKnife\Tests\AbstractTestCase;
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

        $classConstantFetches = $this->classConstantsFetchFinder->find(
            PhpFilesFinder::find([__DIR__ . '/Fixture']),
            $progressBar
        );

        $this->assertCount(3, $classConstantFetches);
    }
}

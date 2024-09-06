<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\PhpParser\Finder\ClassConstFinder;

use Rector\SwissKnife\Contract\ClassConstantFetchInterface;
use Rector\SwissKnife\Finder\PhpFilesFinder;
use Rector\SwissKnife\PhpParser\Finder\ClassConstantFetchFinder;
use Rector\SwissKnife\PhpParser\Finder\ClassConstFinder;
use Rector\SwissKnife\Tests\AbstractTestCase;
use Rector\SwissKnife\ValueObject\ClassConstant;
use Rector\SwissKnife\ValueObject\ClassConstantFetch\CurrentClassConstantFetch;
use Rector\SwissKnife\ValueObject\ClassConstantFetch\ExternalClassAccessConstantFetch;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\NullOutput;

final class ClassConstFinderTest extends AbstractTestCase
{
    private ClassConstFinder $classConstFinder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->classConstFinder = $this->make(ClassConstFinder::class);
    }

    public function test(): void
    {
        $classConstants = $this->findInDirectory(__DIR__ . '/Fixture');
        $this->assertCount(1, $classConstants);
    }

    /**
     * @return ClassConstant[]
     */
    private function findInDirectory(string $directory): array
    {
        $fileInfos = PhpFilesFinder::find([$directory]);

        return $this->classConstFinder->find($fileInfos);
    }
}

<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\PhpParser\Finder\ClassConstFinder;

use Rector\SwissKnife\Finder\PhpFilesFinder;
use Rector\SwissKnife\PhpParser\Finder\ClassConstFinder;
use Rector\SwissKnife\Tests\AbstractTestCase;

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
        $fileInfos = PhpFilesFinder::find([__DIR__ . '/Fixture']);

        $classConstants = $this->classConstFinder->find($fileInfos);

        $this->assertCount(1, $classConstants);
    }
}

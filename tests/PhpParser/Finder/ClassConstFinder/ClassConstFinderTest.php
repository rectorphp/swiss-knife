<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\PhpParser\Finder\ClassConstFinder;

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

    public function testSkipParentConstant(): void
    {
        $classConstants = $this->classConstFinder->find(__DIR__ . '/Fixture/SomeClassWithConstants.php');

        $this->assertCount(1, $classConstants);
    }

    public function testSkipAbstract(): void
    {
        $classConstants = $this->classConstFinder->find(__DIR__ . '/Fixture/AbstractParentClass.php');

        $this->assertCount(0, $classConstants);
    }
}

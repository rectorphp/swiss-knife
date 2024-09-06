<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\PhpParser\Finder\ClassConstFinder;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
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

    #[DataProvider('provideData')]
    public function test(string $filePath, int $expectedClassConstantCount): void
    {
        $classConstants = $this->classConstFinder->find($filePath);
        $this->assertCount($expectedClassConstantCount, $classConstants);
    }

    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SomeClassWithConstants.php', 1];
        yield [__DIR__ . '/Fixture/AbstractParentClass.php', 0];
        yield [__DIR__ . '/Fixture/SomeClassWithInterfaceImplemented.php', 0];
    }
}

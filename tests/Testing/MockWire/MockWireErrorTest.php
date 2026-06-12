<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Testing\MockWire;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\Testing\MockWire;
use Rector\SwissKnife\Tests\Testing\MockWire\Fixture\ClassWithConstructorDependencies;
use Rector\SwissKnife\Tests\Testing\MockWire\Fixture\SecondDependency;

final class MockWireErrorTest extends TestCase
{
    public function testClassNotFound(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Class "NonExistingClass" used in');

        MockWire::create('NonExistingClass', [new \stdClass()]);
    }

    public function testNonObjectDependency(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('All constructor dependencies must be objects');

        MockWire::create(ClassWithConstructorDependencies::class, ['not-an-object']);
    }

    public function testEmptyDependencies(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('empty arguments');

        MockWire::create(ClassWithConstructorDependencies::class, []);
    }

    public function testNoConstructor(): void
    {
        $object = MockWire::create(SecondDependency::class, [new \stdClass()]);

        $this->assertInstanceOf(SecondDependency::class, $object);
    }

    public function testUntypedParameter(): void
    {
        require_once __DIR__ . '/Fixture/ClassWithUntypedParameter.php';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Only typed parameters can be automocked');

        MockWire::create(
            \Rector\SwissKnife\Tests\Testing\MockWire\Fixture\ClassWithUntypedParameter::class,
            [new \stdClass()]
        );
    }
}

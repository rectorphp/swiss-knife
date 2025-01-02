<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Testing\MockWire;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\Testing\MockWire;
use Rector\SwissKnife\Tests\Testing\MockWire\Fixture\ClassWithConstructorDependencies;
use Rector\SwissKnife\Tests\Testing\MockWire\Fixture\SecondDependency;

final class MockWireTest extends TestCase
{
    /**
     * @var string
     */
    private const SECOND_VALUE = 'second';

    public function testOriginalService(): void
    {
        $classWithConstructorDependencies = MockWire::create(ClassWithConstructorDependencies::class, [
            'secondDependency' => $this->createMock(SecondDependency::class),
        ]);

        // we use real object here
        $this->assertInstanceOf(ClassWithConstructorDependencies::class, $classWithConstructorDependencies);
        $this->assertNotInstanceOf(MockObject::class, $classWithConstructorDependencies);
    }

    public function testArguments(): void
    {
        $secondDependencyMock = $this->createMock(SecondDependency::class);
        $secondDependencyMock->method('getName')
            ->willReturn(self::SECOND_VALUE);

        $classWithConstructorDependencies = MockWire::create(ClassWithConstructorDependencies::class, [
            'secondDependency' => $secondDependencyMock,
        ]);

        $this->assertInstanceOf(MockObject::class, $classWithConstructorDependencies->getFirstDependency());

        $providedSecondDependencyMock = $classWithConstructorDependencies->getSecondDependency();
        $this->assertInstanceOf(MockObject::class, $providedSecondDependencyMock);

        $this->assertSame($secondDependencyMock, $providedSecondDependencyMock);
        $this->assertSame(self::SECOND_VALUE, $secondDependencyMock->getName());
    }

    public function testTypeArguments(): void
    {
        $secondDependencyMock = $this->createMock(SecondDependency::class);

        $classWithConstructorDependencies = MockWire::create(ClassWithConstructorDependencies::class, [
            $secondDependencyMock,
        ]);

        $providedSecondDependencyMock = $classWithConstructorDependencies->getSecondDependency();
        $this->assertSame($secondDependencyMock, $providedSecondDependencyMock);
    }

    public function testRealTypeArguments(): void
    {
        $secondDependency = new SecondDependency();

        $classWithConstructorDependencies = MockWire::create(ClassWithConstructorDependencies::class, [
            $secondDependency,
        ]);

        $providedSecondDependencyMock = $classWithConstructorDependencies->getSecondDependency();
        $this->assertSame($secondDependency, $providedSecondDependencyMock);
    }
}

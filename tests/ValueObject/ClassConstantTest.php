<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\ValueObject\ClassConstant;
use Webmozart\Assert\InvalidArgumentException;

final class ClassConstantTest extends TestCase
{
    public function testGetters(): void
    {
        $classConstant = new ClassConstant('SomeClass', 'SOME_CONSTANT');

        $this->assertSame('SomeClass', $classConstant->getClassName());
        $this->assertSame('SOME_CONSTANT', $classConstant->getConstantName());
    }

    public function testEmptyClassName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ClassConstant('', 'SOME_CONSTANT');
    }

    public function testEmptyConstantName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ClassConstant('SomeClass', '');
    }
}

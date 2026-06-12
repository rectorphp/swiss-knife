<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\ValueObject\ClassConstantFetch;

use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\ValueObject\ClassConstant;
use Rector\SwissKnife\ValueObject\ClassConstantFetch\CurrentClassConstantFetch;
use Rector\SwissKnife\ValueObject\ClassConstantFetch\ExternalClassAccessConstantFetch;
use Rector\SwissKnife\ValueObject\ClassConstantFetch\ParentClassConstantFetch;
use Rector\SwissKnife\ValueObject\ClassConstantFetch\StaticClassConstantFetch;

final class AbstractClassConstantFetchTest extends TestCase
{
    public function testCurrentClassConstantFetch(): void
    {
        $fetch = new CurrentClassConstantFetch('SomeClass', 'FOO');
        $this->assertSame('SomeClass', $fetch->getClassName());
        $this->assertSame('FOO', $fetch->getConstantName());

        $this->assertTrue($fetch->isClassConstantMatch(new ClassConstant('SomeClass', 'FOO')));
        $this->assertFalse($fetch->isClassConstantMatch(new ClassConstant('OtherClass', 'FOO')));
        $this->assertFalse($fetch->isClassConstantMatch(new ClassConstant('SomeClass', 'BAR')));
    }

    public function testExternalClassAccessConstantFetch(): void
    {
        $fetch = new ExternalClassAccessConstantFetch('ExternalClass', 'BAR');
        $this->assertTrue($fetch->isClassConstantMatch(new ClassConstant('ExternalClass', 'BAR')));
        $this->assertFalse($fetch->isClassConstantMatch(new ClassConstant('ExternalClass', 'BAZ')));
    }

    public function testParentClassConstantFetch(): void
    {
        $fetch = new ParentClassConstantFetch('ChildClass', 'PARENT_CONST');
        $this->assertSame('ChildClass', $fetch->getClassName());
        $this->assertSame('PARENT_CONST', $fetch->getConstantName());
        $this->assertTrue($fetch->isClassConstantMatch(new ClassConstant('ChildClass', 'PARENT_CONST')));
        $this->assertFalse($fetch->isClassConstantMatch(new ClassConstant('ParentClass', 'PARENT_CONST')));
    }

    public function testStaticClassConstantFetch(): void
    {
        $fetch = new StaticClassConstantFetch('SomeClass', 'STATIC_CONST');
        $this->assertSame('SomeClass', $fetch->getClassName());
        $this->assertSame('STATIC_CONST', $fetch->getConstantName());
        $this->assertTrue($fetch->isClassConstantMatch(new ClassConstant('SomeClass', 'STATIC_CONST')));
        $this->assertFalse($fetch->isClassConstantMatch(new ClassConstant('SomeClass', 'OTHER')));
    }
}

<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\PhpParser\Finder\ClassConstantFetchFinder\Fixture\ParentAndStatic;

final class ClassWithParentAndStatic extends ParentClassWithConstant
{
    public function run(): string
    {
        $parent = self::PARENT_CONSTANT;
        $static = static::PARENT_CONSTANT;

        return $parent . $static;
    }
}

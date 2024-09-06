<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\PhpParser\ClassConstantFetchFinder\Fixture;

final class SomeFileWithConstants
{
    public function run()
    {
        return AnotherClassWithConstant::ANOTHER_CONSTANT;
    }
}

<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\PhpParser\ClassConstantFetchFinder\Fixture;

final class SomeFileWithConstants
{
    private const LOCAL_CONSTANT = 'local';

    public function run()
    {
        $localConstant = self::LOCAL_CONSTANT;

        return AnotherClassWithConstant::ANOTHER_CONSTANT;
    }
}

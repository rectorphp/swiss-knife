<?php

namespace Rector\SwissKnife\Tests\PhpParser\ClassConstantFetchFinder\Fixture\Skip;

enum SomeEnum
{
    public const VALUE = 1000;

    public function run()
    {
        return self::VALUE;
    }
}

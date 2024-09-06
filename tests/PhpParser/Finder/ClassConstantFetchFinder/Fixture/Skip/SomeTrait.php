<?php

namespace Rector\SwissKnife\Tests\PhpParser\Finder\ClassConstantFetchFinder\Fixture\Skip;

trait SomeTrait
{
    public const VALUE = 1000;

    public function run()
    {
        return self::VALUE;
    }
}

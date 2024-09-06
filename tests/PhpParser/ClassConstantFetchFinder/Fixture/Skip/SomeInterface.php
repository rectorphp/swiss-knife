<?php

namespace Rector\SwissKnife\Tests\PhpParser\ClassConstantFetchFinder\Fixture\Skip;

interface SomeInterface
{
    public const VALUE = 1000;

    public function go($input = self::VALUE);
}

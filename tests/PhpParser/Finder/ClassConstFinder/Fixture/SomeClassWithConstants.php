<?php

namespace Rector\SwissKnife\Tests\PhpParser\Finder\ClassConstFinder\Fixture;

final class SomeClassWithConstants extends AbstractParentClass
{
    public const NAME = 'som_value';

    public const SOME_CONST = 'som_value';
}

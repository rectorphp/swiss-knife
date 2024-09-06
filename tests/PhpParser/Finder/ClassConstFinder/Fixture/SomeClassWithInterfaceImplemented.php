<?php

namespace Rector\SwissKnife\Tests\PhpParser\Finder\ClassConstFinder\Fixture;

final class SomeClassWithInterfaceImplemented implements InterfaceWithConstant
{
    public const NAME = 'some_value';
}

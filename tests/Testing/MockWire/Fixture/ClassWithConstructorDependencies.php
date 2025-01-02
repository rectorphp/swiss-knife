<?php

namespace Rector\SwissKnife\Tests\Testing\MockWire\Fixture;

final class ClassWithConstructorDependencies
{
    public function __construct(
        private FirstDependency $firstDependency,
        private SecondDependency $secondDependency,
    ) {
    }

    public function getFirstDependency(): FirstDependency
    {
        return $this->firstDependency;
    }

    public function getSecondDependency(): SecondDependency
    {
        return $this->secondDependency;
    }
}

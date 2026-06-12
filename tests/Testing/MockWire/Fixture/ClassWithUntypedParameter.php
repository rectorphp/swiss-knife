<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Testing\MockWire\Fixture;

final class ClassWithUntypedParameter
{
    public function __construct($dependency)
    {
    }
}

<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Testing;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\Testing\PHPUnitMocker;

final class PHPUnitMockerTest extends TestCase
{
    public function testCreate(): void
    {
        $phpunitMocker = new PHPUnitMocker('testCreate');
        $mock = $phpunitMocker->create(\stdClass::class);

        $this->assertInstanceOf(MockObject::class, $mock);
    }
}

<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\ValueObject\VisibilityChangeStats;

final class VisibilityChangeStatsTest extends TestCase
{
    public function test(): void
    {
        $visibilityChangeStats = new VisibilityChangeStats();
        $this->assertFalse($visibilityChangeStats->hasAnyChange());
        $this->assertSame(0, $visibilityChangeStats->getPrivateCount());

        $visibilityChangeStats->countPrivate();
        $visibilityChangeStats->countPrivate();

        $this->assertTrue($visibilityChangeStats->hasAnyChange());
        $this->assertSame(2, $visibilityChangeStats->getPrivateCount());

        $otherStats = new VisibilityChangeStats();
        $otherStats->countPrivate();

        $visibilityChangeStats->merge($otherStats);
        $this->assertSame(3, $visibilityChangeStats->getPrivateCount());
    }
}

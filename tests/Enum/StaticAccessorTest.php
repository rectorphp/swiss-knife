<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Enum;

use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\Enum\StaticAccessor;

final class StaticAccessorTest extends TestCase
{
    public function testConstants(): void
    {
        $this->assertSame('static', StaticAccessor::STATIC);
        $this->assertSame('self', StaticAccessor::SELF);
    }
}

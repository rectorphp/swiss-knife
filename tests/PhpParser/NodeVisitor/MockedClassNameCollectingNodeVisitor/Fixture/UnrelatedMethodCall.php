<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\PhpParser\NodeVisitor\MockedClassNameCollectingNodeVisitor\Fixture;

use PHPUnit\Framework\TestCase;

final class UnrelatedMethodCall extends TestCase
{
    public function test(): void
    {
        $this->assertTrue(true);
        $this->someRandomMethod(\SomeNamespace\ShouldNotBeDetected::class);
    }
}

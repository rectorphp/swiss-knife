<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\PhpParser\NodeVisitor\MockedClassNameCollectingNodeVisitor\Fixture;

use PHPUnit\Framework\TestCase;

final class CreateMockCall extends TestCase
{
    public function test(): void
    {
        $mock = $this->createMock(\SomeNamespace\SomeMockedClass::class);
    }
}

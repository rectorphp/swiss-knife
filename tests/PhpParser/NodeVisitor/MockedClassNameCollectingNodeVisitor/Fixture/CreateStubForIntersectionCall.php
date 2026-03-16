<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\PhpParser\NodeVisitor\MockedClassNameCollectingNodeVisitor\Fixture;

use PHPUnit\Framework\TestCase;

final class CreateStubForIntersectionCall extends TestCase
{
    public function test(): void
    {
        $stub = $this->createStubForIntersectionOfInterfaces(\SomeNamespace\SomeIntersectionClass::class);
    }
}

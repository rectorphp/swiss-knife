<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\PhpParser\NodeVisitor\MockedClassNameCollectingNodeVisitor\Fixture;

use PHPUnit\Framework\TestCase;

final class CreateConfiguredStubCall extends TestCase
{
    public function test(): void
    {
        $stub = $this->createConfiguredStub(\SomeNamespace\SomeConfiguredStubClass::class, []);
    }
}

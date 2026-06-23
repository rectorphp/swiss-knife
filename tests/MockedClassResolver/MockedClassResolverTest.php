<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\MockedClassResolver;

use Rector\SwissKnife\MockedClassResolver;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class MockedClassResolverTest extends AbstractTestCase
{
    public function testResolve(): void
    {
        $mockedClassResolver = $this->make(MockedClassResolver::class);

        $fixtureDirectory = __DIR__ . '/../PhpParser/NodeVisitor/MockedClassNameCollectingNodeVisitor/Fixture';
        $progressCalls = 0;

        $mockedClassNames = $mockedClassResolver->resolve(
            [$fixtureDirectory],
            static function () use (&$progressCalls): void {
                ++$progressCalls;
            }
        );

        $this->assertSame(
            [
                'SomeNamespace\SomeConfiguredStubClass',
                'SomeNamespace\SomeIntersectionClass',
                'SomeNamespace\SomeMockedClass',
                'SomeNamespace\SomeStubClass',
            ],
            $mockedClassNames
        );
        $this->assertGreaterThan(0, $progressCalls);
    }
}

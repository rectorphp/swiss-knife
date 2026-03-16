<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\PhpParser\NodeVisitor\MockedClassNameCollectingNodeVisitor;

use Iterator;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\PhpParser\NodeVisitor\MockedClassNameCollectingNodeVisitor;

final class MockedClassNameCollectingNodeVisitorTest extends TestCase
{
    /**
     * @param string[] $expectedClassNames
     */
    #[DataProvider('provideData')]
    public function test(string $filePath, array $expectedClassNames): void
    {
        $mockedClassNameCollectingNodeVisitor = new MockedClassNameCollectingNodeVisitor();

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($mockedClassNameCollectingNodeVisitor);

        $parser = (new ParserFactory())->createForNewestSupportedVersion();
        $stmts = $parser->parse((string) file_get_contents($filePath));
        $this->assertNotNull($stmts);

        $nodeTraverser->traverse($stmts);

        $this->assertSame($expectedClassNames, $mockedClassNameCollectingNodeVisitor->getMockedClassNames());
    }

    /**
     * @return Iterator<string, array{string, string[]}>
     */
    public static function provideData(): Iterator
    {
        yield 'createMock' => [__DIR__ . '/Fixture/CreateMockCall.php', ['SomeNamespace\SomeMockedClass']];

        yield 'createStub' => [__DIR__ . '/Fixture/CreateStubCall.php', ['SomeNamespace\SomeStubClass']];

        yield 'createStubForIntersectionOfInterfaces' => [
            __DIR__ . '/Fixture/CreateStubForIntersectionCall.php',
            ['SomeNamespace\SomeIntersectionClass'],
        ];

        yield 'createConfiguredStub' => [
            __DIR__ . '/Fixture/CreateConfiguredStubCall.php',
            ['SomeNamespace\SomeConfiguredStubClass'],
        ];

        yield 'unrelated method is not detected' => [__DIR__ . '/Fixture/UnrelatedMethodCall.php', []];
    }
}

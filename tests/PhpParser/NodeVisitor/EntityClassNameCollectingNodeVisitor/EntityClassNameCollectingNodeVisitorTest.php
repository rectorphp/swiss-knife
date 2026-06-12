<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\PhpParser\NodeVisitor\EntityClassNameCollectingNodeVisitor;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\PhpParser\NodeVisitor\EntityClassNameCollectingNodeVisitor;

final class EntityClassNameCollectingNodeVisitorTest extends TestCase
{
    /**
     * @param string[] $expectedEntityClassNames
     */
    #[DataProvider('provideData')]
    public function test(string $filePath, array $expectedEntityClassNames): void
    {
        $visitor = new EntityClassNameCollectingNodeVisitor();
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new NameResolver());
        $nodeTraverser->addVisitor($visitor);

        $parser = (new ParserFactory())->createForNewestSupportedVersion();
        $stmts = $parser->parse((string) file_get_contents($filePath));
        $this->assertNotNull($stmts);

        $nodeTraverser->traverse($stmts);

        $this->assertSame($expectedEntityClassNames, $visitor->getEntityClassNames());
    }

    /**
     * @return iterable<string, array{string, string[]}>
     */
    public static function provideData(): iterable
    {
        yield 'entity attribute' => [
            __DIR__ . '/Fixture/EntityWithAttribute.php',
            ['Rector\SwissKnife\Tests\PhpParser\NodeVisitor\EntityClassNameCollectingNodeVisitor\Fixture\SomeEntity'],
        ];

        yield 'document annotation' => [
            __DIR__ . '/Fixture/DocumentWithAnnotation.php',
            ['Rector\SwissKnife\Tests\PhpParser\NodeVisitor\EntityClassNameCollectingNodeVisitor\Fixture\SomeDocument'],
        ];

        yield 'non entity' => [__DIR__ . '/Fixture/RegularClass.php', []];
    }
}

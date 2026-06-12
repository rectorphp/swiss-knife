<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\PhpParser\NodeVisitor\FindClassConstFetchNodeVisitor;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\PhpParser\NodeVisitor\FindClassConstFetchNodeVisitor;
use Rector\SwissKnife\ValueObject\ClassConstantFetch\CurrentClassConstantFetch;

final class FindClassConstFetchNodeVisitorExtendedTest extends TestCase
{
    public function testAnonymousClassIsSkipped(): void
    {
        $visitor = new FindClassConstFetchNodeVisitor();
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new NameResolver());
        $nodeTraverser->addVisitor($visitor);

        $parser = (new ParserFactory())->createForNewestSupportedVersion();
        $code = <<<'PHP'
<?php
namespace TestAnonymous;
final class User
{
    public function run(): object
    {
        return new class {
            public const FOO = 'bar';
        };
    }
}
PHP;
        $nodeTraverser->traverse($parser->parse($code));

        $this->assertSame([], $visitor->getClassConstantFetches());
    }

    public function testSelfMagicClassConstantIsSkipped(): void
    {
        $visitor = new FindClassConstFetchNodeVisitor();
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new NameResolver());
        $nodeTraverser->addVisitor($visitor);

        $parser = (new ParserFactory())->createForNewestSupportedVersion();
        $code = <<<'PHP'
<?php
namespace TestMagic;
final class User
{
    public function run(): string
    {
        return self::class;
    }
}
PHP;
        $nodeTraverser->traverse($parser->parse($code));

        $this->assertSame([], $visitor->getClassConstantFetches());
    }

    public function testDynamicConstantNameIsSkipped(): void
    {
        $visitor = new FindClassConstFetchNodeVisitor();
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new NameResolver());
        $nodeTraverser->addVisitor($visitor);

        $parser = (new ParserFactory())->createForNewestSupportedVersion();
        $code = <<<'PHP'
<?php
namespace TestDynamic;
final class User
{
    public const FOO = 'bar';

    public function run(string $name): mixed
    {
        return self::{$name};
    }
}
PHP;
        $nodeTraverser->traverse($parser->parse($code));

        $this->assertSame([], $visitor->getClassConstantFetches());
    }

    public function testCurrentClassConstant(): void
    {
        $visitor = new FindClassConstFetchNodeVisitor();
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new NameResolver());
        $nodeTraverser->addVisitor($visitor);

        $parser = (new ParserFactory())->createForNewestSupportedVersion();
        $code = <<<'PHP'
<?php
namespace TestCurrent;
final class User
{
    public const FOO = 'bar';

    public function run(): string
    {
        return self::FOO;
    }
}
PHP;
        $nodeTraverser->traverse($parser->parse($code));

        $fetches = $visitor->getClassConstantFetches();
        $this->assertCount(1, $fetches);
        $this->assertInstanceOf(CurrentClassConstantFetch::class, $fetches[0]);
    }

    public function testInterfaceExists(): void
    {
        $visitor = new FindClassConstFetchNodeVisitor();
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new NameResolver());
        $nodeTraverser->addVisitor($visitor);

        $parser = (new ParserFactory())->createForNewestSupportedVersion();
        $code = <<<'PHP'
<?php
namespace TestInterface;
final class User
{
    public function run(): string
    {
        return \Countable::class;
    }
}
PHP;
        $nodeTraverser->traverse($parser->parse($code));

        $this->assertSame([], $visitor->getClassConstantFetches());
    }
}

<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\PhpParser\NodeVisitor;

use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Param;
use PhpParser\NodeTraverser;
use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\PhpParser\NodeVisitor\AddImportConfigMethodCallNodeVisitor;

final class AddImportConfigMethodCallNodeVisitorTest extends TestCase
{
    public function testAddImportCall(): void
    {
        $closure = new Closure();
        $closure->params[] = new Param(new Variable('containerConfigurator'), null, new Identifier('ContainerConfigurator'));

        $addImportConfigMethodCallNodeVisitor = new AddImportConfigMethodCallNodeVisitor('/config/packages');
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($addImportConfigMethodCallNodeVisitor);

        $nodeTraverser->traverse([$closure]);

        $this->assertCount(1, $closure->stmts);
    }
}

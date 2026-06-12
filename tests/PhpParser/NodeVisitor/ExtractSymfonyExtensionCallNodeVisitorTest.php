<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\PhpParser\NodeVisitor;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Expr\Variable;
use PhpParser\NodeTraverser;
use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\PhpParser\NodeVisitor\ExtractSymfonyExtensionCallNodeVisitor;

final class ExtractSymfonyExtensionCallNodeVisitorTest extends TestCase
{
    public function testExtractExtensionCalls(): void
    {
        $extensionCall = new Expression(new MethodCall(
            new Variable('containerConfigurator'),
            new Identifier('extension'),
            [new Arg(new String_('framework'))]
        ));
        $otherCall = new Expression(new MethodCall(
            new Variable('containerConfigurator'),
            new Identifier('import'),
            [new Arg(new String_('services.php'))]
        ));

        $extractSymfonyExtensionCallNodeVisitor = new ExtractSymfonyExtensionCallNodeVisitor();
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($extractSymfonyExtensionCallNodeVisitor);

        $stmts = $nodeTraverser->traverse([$extensionCall, $otherCall]);

        $this->assertCount(1, $stmts);
        $this->assertCount(1, $extractSymfonyExtensionCallNodeVisitor->getExtensionMethodCalls());
    }
}

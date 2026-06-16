<?php

declare (strict_types=1);
namespace SwissKnife202606\TomasVotruba\ClassLeak\NodeDecorator;

use SwissKnife202606\PhpParser\Node\Stmt;
use SwissKnife202606\PhpParser\NodeTraverser;
use SwissKnife202606\PhpParser\NodeVisitor\NameResolver;
use SwissKnife202606\PhpParser\NodeVisitor\NodeConnectingVisitor;
final class FullyQualifiedNameNodeDecorator
{
    /**
     * @param Stmt[] $stmts
     */
    public function decorate(array $stmts) : void
    {
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new NameResolver());
        $nodeTraverser->addVisitor(new NodeConnectingVisitor());
        $nodeTraverser->traverse($stmts);
    }
}

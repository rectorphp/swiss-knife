<?php

declare (strict_types=1);
namespace Symplify\EasyCI\ActiveClass\NodeDecorator;

use EasyCI202209\PhpParser\Node\Stmt;
use EasyCI202209\PhpParser\NodeTraverser;
use EasyCI202209\PhpParser\NodeVisitor\NameResolver;
use EasyCI202209\PhpParser\NodeVisitor\NodeConnectingVisitor;
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

<?php

declare (strict_types=1);
namespace EasyCI20220607\Symplify\EasyCI\ActiveClass\NodeDecorator;

use EasyCI20220607\PhpParser\Node\Stmt;
use EasyCI20220607\PhpParser\NodeTraverser;
use EasyCI20220607\PhpParser\NodeVisitor\NameResolver;
use EasyCI20220607\PhpParser\NodeVisitor\NodeConnectingVisitor;
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

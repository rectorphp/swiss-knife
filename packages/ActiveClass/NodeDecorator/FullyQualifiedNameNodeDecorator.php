<?php

declare (strict_types=1);
namespace Symplify\EasyCI\ActiveClass\NodeDecorator;

use EasyCI20220305\PhpParser\Node\Stmt;
use EasyCI20220305\PhpParser\NodeTraverser;
use EasyCI20220305\PhpParser\NodeVisitor\NameResolver;
use EasyCI20220305\PhpParser\NodeVisitor\NodeConnectingVisitor;
final class FullyQualifiedNameNodeDecorator
{
    /**
     * @param Stmt[] $stmts
     */
    public function decorate(array $stmts) : void
    {
        $nodeTraverser = new \EasyCI20220305\PhpParser\NodeTraverser();
        $nodeTraverser->addVisitor(new \EasyCI20220305\PhpParser\NodeVisitor\NameResolver());
        $nodeTraverser->addVisitor(new \EasyCI20220305\PhpParser\NodeVisitor\NodeConnectingVisitor());
        $nodeTraverser->traverse($stmts);
    }
}

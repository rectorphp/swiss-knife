<?php

declare (strict_types=1);
namespace EasyCI202208\Symplify\Astral\NodeNameResolver;

use EasyCI202208\PhpParser\Node;
use EasyCI202208\PhpParser\Node\Expr;
use EasyCI202208\PhpParser\Node\Expr\FuncCall;
use EasyCI202208\Symplify\Astral\Contract\NodeNameResolverInterface;
final class FuncCallNodeNameResolver implements NodeNameResolverInterface
{
    public function match(Node $node) : bool
    {
        return $node instanceof FuncCall;
    }
    /**
     * @param FuncCall $node
     */
    public function resolve(Node $node) : ?string
    {
        if ($node->name instanceof Expr) {
            return null;
        }
        return (string) $node->name;
    }
}

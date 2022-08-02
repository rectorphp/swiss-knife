<?php

declare (strict_types=1);
namespace EasyCI202208\Symplify\Astral\NodeNameResolver;

use EasyCI202208\PhpParser\Node;
use EasyCI202208\PhpParser\Node\Expr;
use EasyCI202208\PhpParser\Node\Param;
use EasyCI202208\Symplify\Astral\Contract\NodeNameResolverInterface;
final class ParamNodeNameResolver implements NodeNameResolverInterface
{
    public function match(Node $node) : bool
    {
        return $node instanceof Param;
    }
    /**
     * @param Param $node
     */
    public function resolve(Node $node) : ?string
    {
        $paramName = $node->var->name;
        if ($paramName instanceof Expr) {
            return null;
        }
        return $paramName;
    }
}

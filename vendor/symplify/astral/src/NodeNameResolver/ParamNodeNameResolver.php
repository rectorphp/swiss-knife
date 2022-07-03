<?php

declare (strict_types=1);
namespace EasyCI202207\Symplify\Astral\NodeNameResolver;

use EasyCI202207\PhpParser\Node;
use EasyCI202207\PhpParser\Node\Expr;
use EasyCI202207\PhpParser\Node\Param;
use EasyCI202207\Symplify\Astral\Contract\NodeNameResolverInterface;
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

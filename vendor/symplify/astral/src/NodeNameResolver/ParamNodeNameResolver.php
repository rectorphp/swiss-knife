<?php

declare (strict_types=1);
namespace EasyCI20220612\Symplify\Astral\NodeNameResolver;

use EasyCI20220612\PhpParser\Node;
use EasyCI20220612\PhpParser\Node\Expr;
use EasyCI20220612\PhpParser\Node\Param;
use EasyCI20220612\Symplify\Astral\Contract\NodeNameResolverInterface;
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

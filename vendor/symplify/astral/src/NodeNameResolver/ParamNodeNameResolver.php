<?php

declare (strict_types=1);
namespace EasyCI20220602\Symplify\Astral\NodeNameResolver;

use EasyCI20220602\PhpParser\Node;
use EasyCI20220602\PhpParser\Node\Expr;
use EasyCI20220602\PhpParser\Node\Param;
use EasyCI20220602\Symplify\Astral\Contract\NodeNameResolverInterface;
final class ParamNodeNameResolver implements \EasyCI20220602\Symplify\Astral\Contract\NodeNameResolverInterface
{
    public function match(\EasyCI20220602\PhpParser\Node $node) : bool
    {
        return $node instanceof \EasyCI20220602\PhpParser\Node\Param;
    }
    /**
     * @param Param $node
     */
    public function resolve(\EasyCI20220602\PhpParser\Node $node) : ?string
    {
        $paramName = $node->var->name;
        if ($paramName instanceof \EasyCI20220602\PhpParser\Node\Expr) {
            return null;
        }
        return $paramName;
    }
}

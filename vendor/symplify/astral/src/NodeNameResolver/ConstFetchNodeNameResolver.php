<?php

declare (strict_types=1);
namespace EasyCI20220610\Symplify\Astral\NodeNameResolver;

use EasyCI20220610\PhpParser\Node;
use EasyCI20220610\PhpParser\Node\Expr\ConstFetch;
use EasyCI20220610\Symplify\Astral\Contract\NodeNameResolverInterface;
final class ConstFetchNodeNameResolver implements NodeNameResolverInterface
{
    public function match(Node $node) : bool
    {
        return $node instanceof ConstFetch;
    }
    /**
     * @param ConstFetch $node
     */
    public function resolve(Node $node) : ?string
    {
        return $node->name->toString();
    }
}

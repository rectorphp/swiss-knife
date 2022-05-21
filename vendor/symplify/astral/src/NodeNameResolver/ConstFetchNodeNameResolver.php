<?php

declare (strict_types=1);
namespace EasyCI20220521\Symplify\Astral\NodeNameResolver;

use EasyCI20220521\PhpParser\Node;
use EasyCI20220521\PhpParser\Node\Expr\ConstFetch;
use EasyCI20220521\Symplify\Astral\Contract\NodeNameResolverInterface;
final class ConstFetchNodeNameResolver implements \EasyCI20220521\Symplify\Astral\Contract\NodeNameResolverInterface
{
    public function match(\EasyCI20220521\PhpParser\Node $node) : bool
    {
        return $node instanceof \EasyCI20220521\PhpParser\Node\Expr\ConstFetch;
    }
    /**
     * @param ConstFetch $node
     */
    public function resolve(\EasyCI20220521\PhpParser\Node $node) : ?string
    {
        return $node->name->toString();
    }
}

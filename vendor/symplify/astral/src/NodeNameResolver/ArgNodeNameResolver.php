<?php

declare (strict_types=1);
namespace EasyCI20220611\Symplify\Astral\NodeNameResolver;

use EasyCI20220611\PhpParser\Node;
use EasyCI20220611\PhpParser\Node\Arg;
use EasyCI20220611\Symplify\Astral\Contract\NodeNameResolverInterface;
final class ArgNodeNameResolver implements NodeNameResolverInterface
{
    public function match(Node $node) : bool
    {
        return $node instanceof Arg;
    }
    /**
     * @param Arg $node
     */
    public function resolve(Node $node) : ?string
    {
        if ($node->name === null) {
            return null;
        }
        return (string) $node->name;
    }
}

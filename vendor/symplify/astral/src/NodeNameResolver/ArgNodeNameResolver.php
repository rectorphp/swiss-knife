<?php

declare (strict_types=1);
namespace EasyCI20220516\Symplify\Astral\NodeNameResolver;

use EasyCI20220516\PhpParser\Node;
use EasyCI20220516\PhpParser\Node\Arg;
use EasyCI20220516\Symplify\Astral\Contract\NodeNameResolverInterface;
final class ArgNodeNameResolver implements \EasyCI20220516\Symplify\Astral\Contract\NodeNameResolverInterface
{
    public function match(\EasyCI20220516\PhpParser\Node $node) : bool
    {
        return $node instanceof \EasyCI20220516\PhpParser\Node\Arg;
    }
    /**
     * @param Arg $node
     */
    public function resolve(\EasyCI20220516\PhpParser\Node $node) : ?string
    {
        if ($node->name === null) {
            return null;
        }
        return (string) $node->name;
    }
}

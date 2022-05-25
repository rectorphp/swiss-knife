<?php

declare (strict_types=1);
namespace EasyCI20220525\Symplify\Astral\NodeNameResolver;

use EasyCI20220525\PhpParser\Node;
use EasyCI20220525\PhpParser\Node\Arg;
use EasyCI20220525\Symplify\Astral\Contract\NodeNameResolverInterface;
final class ArgNodeNameResolver implements \EasyCI20220525\Symplify\Astral\Contract\NodeNameResolverInterface
{
    public function match(\EasyCI20220525\PhpParser\Node $node) : bool
    {
        return $node instanceof \EasyCI20220525\PhpParser\Node\Arg;
    }
    /**
     * @param Arg $node
     */
    public function resolve(\EasyCI20220525\PhpParser\Node $node) : ?string
    {
        if ($node->name === null) {
            return null;
        }
        return (string) $node->name;
    }
}

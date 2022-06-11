<?php

declare (strict_types=1);
namespace EasyCI20220611\Symplify\Astral\NodeNameResolver;

use EasyCI20220611\PhpParser\Node;
use EasyCI20220611\PhpParser\Node\Identifier;
use EasyCI20220611\PhpParser\Node\Name;
use EasyCI20220611\Symplify\Astral\Contract\NodeNameResolverInterface;
final class IdentifierNodeNameResolver implements NodeNameResolverInterface
{
    public function match(Node $node) : bool
    {
        if ($node instanceof Identifier) {
            return \true;
        }
        return $node instanceof Name;
    }
    /**
     * @param Identifier|Name $node
     */
    public function resolve(Node $node) : ?string
    {
        return (string) $node;
    }
}

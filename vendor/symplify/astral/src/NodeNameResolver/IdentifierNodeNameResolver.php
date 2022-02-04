<?php

declare (strict_types=1);
namespace EasyCI20220204\Symplify\Astral\NodeNameResolver;

use EasyCI20220204\PhpParser\Node;
use EasyCI20220204\PhpParser\Node\Identifier;
use EasyCI20220204\PhpParser\Node\Name;
use EasyCI20220204\Symplify\Astral\Contract\NodeNameResolverInterface;
final class IdentifierNodeNameResolver implements \EasyCI20220204\Symplify\Astral\Contract\NodeNameResolverInterface
{
    public function match(\EasyCI20220204\PhpParser\Node $node) : bool
    {
        if ($node instanceof \EasyCI20220204\PhpParser\Node\Identifier) {
            return \true;
        }
        return $node instanceof \EasyCI20220204\PhpParser\Node\Name;
    }
    /**
     * @param Identifier|Name $node
     */
    public function resolve(\EasyCI20220204\PhpParser\Node $node) : ?string
    {
        return (string) $node;
    }
}

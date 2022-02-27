<?php

declare (strict_types=1);
namespace EasyCI20220227\Symplify\Astral\NodeNameResolver;

use EasyCI20220227\PhpParser\Node;
use EasyCI20220227\PhpParser\Node\Attribute;
use EasyCI20220227\Symplify\Astral\Contract\NodeNameResolverInterface;
final class AttributeNodeNameResolver implements \EasyCI20220227\Symplify\Astral\Contract\NodeNameResolverInterface
{
    public function match(\EasyCI20220227\PhpParser\Node $node) : bool
    {
        return $node instanceof \EasyCI20220227\PhpParser\Node\Attribute;
    }
    /**
     * @param Attribute $node
     */
    public function resolve(\EasyCI20220227\PhpParser\Node $node) : ?string
    {
        return $node->name->toString();
    }
}

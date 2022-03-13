<?php

declare (strict_types=1);
namespace EasyCI20220313\Symplify\Astral\NodeNameResolver;

use EasyCI20220313\PhpParser\Node;
use EasyCI20220313\PhpParser\Node\Stmt\Property;
use EasyCI20220313\Symplify\Astral\Contract\NodeNameResolverInterface;
final class PropertyNodeNameResolver implements \EasyCI20220313\Symplify\Astral\Contract\NodeNameResolverInterface
{
    public function match(\EasyCI20220313\PhpParser\Node $node) : bool
    {
        return $node instanceof \EasyCI20220313\PhpParser\Node\Stmt\Property;
    }
    /**
     * @param Property $node
     */
    public function resolve(\EasyCI20220313\PhpParser\Node $node) : ?string
    {
        $propertyProperty = $node->props[0];
        return (string) $propertyProperty->name;
    }
}

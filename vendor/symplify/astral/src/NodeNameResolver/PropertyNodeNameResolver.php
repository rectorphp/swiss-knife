<?php

declare (strict_types=1);
namespace EasyCI20220130\Symplify\Astral\NodeNameResolver;

use EasyCI20220130\PhpParser\Node;
use EasyCI20220130\PhpParser\Node\Stmt\Property;
use EasyCI20220130\Symplify\Astral\Contract\NodeNameResolverInterface;
final class PropertyNodeNameResolver implements \EasyCI20220130\Symplify\Astral\Contract\NodeNameResolverInterface
{
    public function match(\EasyCI20220130\PhpParser\Node $node) : bool
    {
        return $node instanceof \EasyCI20220130\PhpParser\Node\Stmt\Property;
    }
    /**
     * @param Property $node
     */
    public function resolve(\EasyCI20220130\PhpParser\Node $node) : ?string
    {
        $propertyProperty = $node->props[0];
        return (string) $propertyProperty->name;
    }
}

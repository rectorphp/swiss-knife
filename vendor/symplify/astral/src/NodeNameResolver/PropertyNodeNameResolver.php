<?php

declare (strict_types=1);
namespace EasyCI20220429\Symplify\Astral\NodeNameResolver;

use EasyCI20220429\PhpParser\Node;
use EasyCI20220429\PhpParser\Node\Stmt\Property;
use EasyCI20220429\Symplify\Astral\Contract\NodeNameResolverInterface;
final class PropertyNodeNameResolver implements \EasyCI20220429\Symplify\Astral\Contract\NodeNameResolverInterface
{
    public function match(\EasyCI20220429\PhpParser\Node $node) : bool
    {
        return $node instanceof \EasyCI20220429\PhpParser\Node\Stmt\Property;
    }
    /**
     * @param Property $node
     */
    public function resolve(\EasyCI20220429\PhpParser\Node $node) : ?string
    {
        $propertyProperty = $node->props[0];
        return (string) $propertyProperty->name;
    }
}

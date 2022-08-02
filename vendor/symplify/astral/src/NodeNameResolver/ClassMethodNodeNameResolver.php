<?php

declare (strict_types=1);
namespace EasyCI202208\Symplify\Astral\NodeNameResolver;

use EasyCI202208\PhpParser\Node;
use EasyCI202208\PhpParser\Node\Stmt\ClassMethod;
use EasyCI202208\Symplify\Astral\Contract\NodeNameResolverInterface;
final class ClassMethodNodeNameResolver implements NodeNameResolverInterface
{
    public function match(Node $node) : bool
    {
        return $node instanceof ClassMethod;
    }
    /**
     * @param ClassMethod $node
     */
    public function resolve(Node $node) : ?string
    {
        return $node->name->toString();
    }
}

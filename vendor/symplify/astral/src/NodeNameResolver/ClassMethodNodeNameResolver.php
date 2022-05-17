<?php

declare (strict_types=1);
namespace EasyCI20220517\Symplify\Astral\NodeNameResolver;

use EasyCI20220517\PhpParser\Node;
use EasyCI20220517\PhpParser\Node\Stmt\ClassMethod;
use EasyCI20220517\Symplify\Astral\Contract\NodeNameResolverInterface;
final class ClassMethodNodeNameResolver implements \EasyCI20220517\Symplify\Astral\Contract\NodeNameResolverInterface
{
    public function match(\EasyCI20220517\PhpParser\Node $node) : bool
    {
        return $node instanceof \EasyCI20220517\PhpParser\Node\Stmt\ClassMethod;
    }
    /**
     * @param ClassMethod $node
     */
    public function resolve(\EasyCI20220517\PhpParser\Node $node) : ?string
    {
        return $node->name->toString();
    }
}

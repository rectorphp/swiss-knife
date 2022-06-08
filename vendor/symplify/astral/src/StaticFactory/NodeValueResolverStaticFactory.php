<?php

declare (strict_types=1);
namespace EasyCI20220608\Symplify\Astral\StaticFactory;

use EasyCI20220608\PhpParser\NodeFinder;
use EasyCI20220608\Symplify\Astral\NodeFinder\SimpleNodeFinder;
use EasyCI20220608\Symplify\Astral\NodeValue\NodeValueResolver;
use EasyCI20220608\Symplify\PackageBuilder\Php\TypeChecker;
/**
 * @api
 */
final class NodeValueResolverStaticFactory
{
    public static function create() : NodeValueResolver
    {
        $simpleNameResolver = SimpleNameResolverStaticFactory::create();
        $simpleNodeFinder = new SimpleNodeFinder(new NodeFinder());
        return new NodeValueResolver($simpleNameResolver, new TypeChecker(), $simpleNodeFinder);
    }
}

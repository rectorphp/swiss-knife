<?php

declare (strict_types=1);
namespace EasyCI20220607\Symplify\Astral\StaticFactory;

use EasyCI20220607\PhpParser\NodeFinder;
use EasyCI20220607\Symplify\Astral\NodeFinder\SimpleNodeFinder;
use EasyCI20220607\Symplify\Astral\NodeValue\NodeValueResolver;
use EasyCI20220607\Symplify\PackageBuilder\Php\TypeChecker;
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

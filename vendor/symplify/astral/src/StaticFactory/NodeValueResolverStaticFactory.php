<?php

declare (strict_types=1);
namespace EasyCI20220414\Symplify\Astral\StaticFactory;

use EasyCI20220414\PhpParser\NodeFinder;
use EasyCI20220414\Symplify\Astral\NodeFinder\SimpleNodeFinder;
use EasyCI20220414\Symplify\Astral\NodeValue\NodeValueResolver;
use EasyCI20220414\Symplify\PackageBuilder\Php\TypeChecker;
/**
 * @api
 */
final class NodeValueResolverStaticFactory
{
    public static function create() : \EasyCI20220414\Symplify\Astral\NodeValue\NodeValueResolver
    {
        $simpleNameResolver = \EasyCI20220414\Symplify\Astral\StaticFactory\SimpleNameResolverStaticFactory::create();
        $simpleNodeFinder = new \EasyCI20220414\Symplify\Astral\NodeFinder\SimpleNodeFinder(new \EasyCI20220414\PhpParser\NodeFinder());
        return new \EasyCI20220414\Symplify\Astral\NodeValue\NodeValueResolver($simpleNameResolver, new \EasyCI20220414\Symplify\PackageBuilder\Php\TypeChecker(), $simpleNodeFinder);
    }
}

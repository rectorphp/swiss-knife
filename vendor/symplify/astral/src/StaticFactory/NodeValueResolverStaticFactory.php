<?php

declare (strict_types=1);
namespace EasyCI20220124\Symplify\Astral\StaticFactory;

use EasyCI20220124\PhpParser\NodeFinder;
use EasyCI20220124\Symplify\Astral\NodeFinder\SimpleNodeFinder;
use EasyCI20220124\Symplify\Astral\NodeValue\NodeValueResolver;
use EasyCI20220124\Symplify\PackageBuilder\Php\TypeChecker;
/**
 * @api
 */
final class NodeValueResolverStaticFactory
{
    public static function create() : \EasyCI20220124\Symplify\Astral\NodeValue\NodeValueResolver
    {
        $simpleNameResolver = \EasyCI20220124\Symplify\Astral\StaticFactory\SimpleNameResolverStaticFactory::create();
        $simpleNodeFinder = new \EasyCI20220124\Symplify\Astral\NodeFinder\SimpleNodeFinder(new \EasyCI20220124\Symplify\PackageBuilder\Php\TypeChecker(), new \EasyCI20220124\PhpParser\NodeFinder());
        return new \EasyCI20220124\Symplify\Astral\NodeValue\NodeValueResolver($simpleNameResolver, new \EasyCI20220124\Symplify\PackageBuilder\Php\TypeChecker(), $simpleNodeFinder);
    }
}

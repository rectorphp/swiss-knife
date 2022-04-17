<?php

declare (strict_types=1);
namespace EasyCI20220417\Symplify\Astral\StaticFactory;

use EasyCI20220417\PhpParser\NodeFinder;
use EasyCI20220417\Symplify\Astral\NodeFinder\SimpleNodeFinder;
use EasyCI20220417\Symplify\Astral\NodeValue\NodeValueResolver;
use EasyCI20220417\Symplify\PackageBuilder\Php\TypeChecker;
/**
 * @api
 */
final class NodeValueResolverStaticFactory
{
    public static function create() : \EasyCI20220417\Symplify\Astral\NodeValue\NodeValueResolver
    {
        $simpleNameResolver = \EasyCI20220417\Symplify\Astral\StaticFactory\SimpleNameResolverStaticFactory::create();
        $simpleNodeFinder = new \EasyCI20220417\Symplify\Astral\NodeFinder\SimpleNodeFinder(new \EasyCI20220417\PhpParser\NodeFinder());
        return new \EasyCI20220417\Symplify\Astral\NodeValue\NodeValueResolver($simpleNameResolver, new \EasyCI20220417\Symplify\PackageBuilder\Php\TypeChecker(), $simpleNodeFinder);
    }
}

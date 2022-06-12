<?php

declare (strict_types=1);
namespace EasyCI20220612\Symplify\Astral\StaticFactory;

use EasyCI20220612\Symplify\Astral\NodeValue\NodeValueResolver;
use EasyCI20220612\Symplify\PackageBuilder\Php\TypeChecker;
/**
 * @api
 */
final class NodeValueResolverStaticFactory
{
    public static function create() : NodeValueResolver
    {
        $simpleNameResolver = SimpleNameResolverStaticFactory::create();
        return new NodeValueResolver($simpleNameResolver, new TypeChecker());
    }
}

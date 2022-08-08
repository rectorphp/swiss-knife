<?php

declare (strict_types=1);
namespace EasyCI202208\Symplify\Astral\StaticFactory;

use EasyCI202208\Symplify\Astral\NodeValue\NodeValueResolver;
use EasyCI202208\Symplify\PackageBuilder\Php\TypeChecker;
/**
 * @api
 */
final class NodeValueResolverStaticFactory
{
    public static function create() : NodeValueResolver
    {
        return new NodeValueResolver(new TypeChecker());
    }
}

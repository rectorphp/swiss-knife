<?php

declare (strict_types=1);
namespace EasyCI202206\Symplify\Astral\StaticFactory;

use EasyCI202206\Symplify\Astral\Naming\SimpleNameResolver;
use EasyCI202206\Symplify\Astral\NodeNameResolver\ArgNodeNameResolver;
use EasyCI202206\Symplify\Astral\NodeNameResolver\AttributeNodeNameResolver;
use EasyCI202206\Symplify\Astral\NodeNameResolver\ClassLikeNodeNameResolver;
use EasyCI202206\Symplify\Astral\NodeNameResolver\ClassMethodNodeNameResolver;
use EasyCI202206\Symplify\Astral\NodeNameResolver\ConstFetchNodeNameResolver;
use EasyCI202206\Symplify\Astral\NodeNameResolver\FuncCallNodeNameResolver;
use EasyCI202206\Symplify\Astral\NodeNameResolver\IdentifierNodeNameResolver;
use EasyCI202206\Symplify\Astral\NodeNameResolver\NamespaceNodeNameResolver;
use EasyCI202206\Symplify\Astral\NodeNameResolver\ParamNodeNameResolver;
use EasyCI202206\Symplify\Astral\NodeNameResolver\PropertyNodeNameResolver;
/**
 * This would be normally handled by standard Symfony or Nette DI, but PHPStan does not use any of those, so we have to
 * make it manually.
 */
final class SimpleNameResolverStaticFactory
{
    public static function create() : SimpleNameResolver
    {
        $nameResolvers = [new ArgNodeNameResolver(), new AttributeNodeNameResolver(), new ClassLikeNodeNameResolver(), new ClassMethodNodeNameResolver(), new ConstFetchNodeNameResolver(), new FuncCallNodeNameResolver(), new IdentifierNodeNameResolver(), new NamespaceNodeNameResolver(), new ParamNodeNameResolver(), new PropertyNodeNameResolver()];
        return new SimpleNameResolver($nameResolvers);
    }
}

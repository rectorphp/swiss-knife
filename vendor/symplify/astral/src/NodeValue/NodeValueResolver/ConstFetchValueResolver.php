<?php

declare (strict_types=1);
namespace EasyCI20220206\Symplify\Astral\NodeValue\NodeValueResolver;

use EasyCI20220206\PhpParser\Node\Expr;
use EasyCI20220206\PhpParser\Node\Expr\ConstFetch;
use EasyCI20220206\Symplify\Astral\Contract\NodeValueResolver\NodeValueResolverInterface;
use EasyCI20220206\Symplify\Astral\Naming\SimpleNameResolver;
/**
 * @see \Symplify\Astral\Tests\NodeValue\NodeValueResolverTest
 *
 * @implements NodeValueResolverInterface<ConstFetch>
 */
final class ConstFetchValueResolver implements \EasyCI20220206\Symplify\Astral\Contract\NodeValueResolver\NodeValueResolverInterface
{
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    public function __construct(\EasyCI20220206\Symplify\Astral\Naming\SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
    }
    public function getType() : string
    {
        return \EasyCI20220206\PhpParser\Node\Expr\ConstFetch::class;
    }
    /**
     * @param ConstFetch $expr
     * @return null|mixed
     */
    public function resolve(\EasyCI20220206\PhpParser\Node\Expr $expr, string $currentFilePath)
    {
        $constFetchName = $this->simpleNameResolver->getName($expr);
        if ($constFetchName === null) {
            return null;
        }
        return \constant($constFetchName);
    }
}

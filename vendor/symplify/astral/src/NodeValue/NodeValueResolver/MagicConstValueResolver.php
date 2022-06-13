<?php

declare (strict_types=1);
namespace EasyCI202206\Symplify\Astral\NodeValue\NodeValueResolver;

use EasyCI202206\PhpParser\Node\Expr;
use EasyCI202206\PhpParser\Node\Scalar\MagicConst;
use EasyCI202206\PhpParser\Node\Scalar\MagicConst\Dir;
use EasyCI202206\PhpParser\Node\Scalar\MagicConst\File;
use EasyCI202206\Symplify\Astral\Contract\NodeValueResolver\NodeValueResolverInterface;
/**
 * @see \Symplify\Astral\Tests\NodeValue\NodeValueResolverTest
 *
 * @implements NodeValueResolverInterface<MagicConst>
 */
final class MagicConstValueResolver implements NodeValueResolverInterface
{
    public function getType() : string
    {
        return MagicConst::class;
    }
    /**
     * @param MagicConst $expr
     */
    public function resolve(Expr $expr, string $currentFilePath) : ?string
    {
        if ($expr instanceof Dir) {
            return \dirname($currentFilePath);
        }
        if ($expr instanceof File) {
            return $currentFilePath;
        }
        return null;
    }
}

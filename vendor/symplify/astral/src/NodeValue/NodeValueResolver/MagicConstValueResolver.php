<?php

declare (strict_types=1);
namespace EasyCI20220503\Symplify\Astral\NodeValue\NodeValueResolver;

use EasyCI20220503\PhpParser\Node\Expr;
use EasyCI20220503\PhpParser\Node\Scalar\MagicConst;
use EasyCI20220503\PhpParser\Node\Scalar\MagicConst\Dir;
use EasyCI20220503\PhpParser\Node\Scalar\MagicConst\File;
use EasyCI20220503\Symplify\Astral\Contract\NodeValueResolver\NodeValueResolverInterface;
/**
 * @see \Symplify\Astral\Tests\NodeValue\NodeValueResolverTest
 *
 * @implements NodeValueResolverInterface<MagicConst>
 */
final class MagicConstValueResolver implements \EasyCI20220503\Symplify\Astral\Contract\NodeValueResolver\NodeValueResolverInterface
{
    public function getType() : string
    {
        return \EasyCI20220503\PhpParser\Node\Scalar\MagicConst::class;
    }
    /**
     * @param MagicConst $expr
     */
    public function resolve(\EasyCI20220503\PhpParser\Node\Expr $expr, string $currentFilePath) : ?string
    {
        if ($expr instanceof \EasyCI20220503\PhpParser\Node\Scalar\MagicConst\Dir) {
            return \dirname($currentFilePath);
        }
        if ($expr instanceof \EasyCI20220503\PhpParser\Node\Scalar\MagicConst\File) {
            return $currentFilePath;
        }
        return null;
    }
}

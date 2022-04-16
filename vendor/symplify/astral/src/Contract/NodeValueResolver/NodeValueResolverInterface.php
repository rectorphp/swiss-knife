<?php

declare (strict_types=1);
namespace EasyCI20220416\Symplify\Astral\Contract\NodeValueResolver;

use EasyCI20220416\PhpParser\Node\Expr;
/**
 * @template TExpr as Expr
 */
interface NodeValueResolverInterface
{
    /**
     * @return class-string<TExpr>
     */
    public function getType() : string;
    /**
     * @param TExpr $expr
     * @return mixed
     */
    public function resolve(\EasyCI20220416\PhpParser\Node\Expr $expr, string $currentFilePath);
}

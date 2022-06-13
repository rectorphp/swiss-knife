<?php

declare (strict_types=1);
namespace EasyCI202206\PHPStan\PhpDocParser\Ast\ConstExpr;

use EasyCI202206\PHPStan\PhpDocParser\Ast\NodeAttributes;
class ConstExprFalseNode implements ConstExprNode
{
    use NodeAttributes;
    public function __toString() : string
    {
        return 'false';
    }
}

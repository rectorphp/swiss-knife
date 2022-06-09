<?php

declare (strict_types=1);
namespace EasyCI20220609\PHPStan\PhpDocParser\Ast\ConstExpr;

use EasyCI20220609\PHPStan\PhpDocParser\Ast\NodeAttributes;
class ConstExprNullNode implements ConstExprNode
{
    use NodeAttributes;
    public function __toString() : string
    {
        return 'null';
    }
}

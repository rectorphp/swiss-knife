<?php

declare (strict_types=1);
namespace EasyCI20220611\PHPStan\PhpDocParser\Ast\ConstExpr;

use EasyCI20220611\PHPStan\PhpDocParser\Ast\NodeAttributes;
class ConstExprTrueNode implements ConstExprNode
{
    use NodeAttributes;
    public function __toString() : string
    {
        return 'true';
    }
}

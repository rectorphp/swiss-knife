<?php

declare (strict_types=1);
namespace EasyCI20220607\PhpParser\Node\Expr\BinaryOp;

use EasyCI20220607\PhpParser\Node\Expr\BinaryOp;
class Div extends BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '/';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_Div';
    }
}

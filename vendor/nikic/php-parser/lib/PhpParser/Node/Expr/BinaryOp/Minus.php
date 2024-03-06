<?php

declare (strict_types=1);
namespace SwissKnife202403\PhpParser\Node\Expr\BinaryOp;

use SwissKnife202403\PhpParser\Node\Expr\BinaryOp;
class Minus extends BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '-';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_Minus';
    }
}

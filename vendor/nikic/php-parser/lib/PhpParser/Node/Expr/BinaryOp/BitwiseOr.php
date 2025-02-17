<?php

declare (strict_types=1);
namespace SwissKnife202502\PhpParser\Node\Expr\BinaryOp;

use SwissKnife202502\PhpParser\Node\Expr\BinaryOp;
class BitwiseOr extends BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '|';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_BitwiseOr';
    }
}

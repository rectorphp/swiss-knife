<?php

declare (strict_types=1);
namespace SwissKnife202507\PhpParser\Node\Expr\BinaryOp;

use SwissKnife202507\PhpParser\Node\Expr\BinaryOp;
class BitwiseXor extends BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '^';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_BitwiseXor';
    }
}

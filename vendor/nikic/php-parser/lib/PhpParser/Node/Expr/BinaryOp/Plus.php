<?php

declare (strict_types=1);
namespace SwissKnife202409\PhpParser\Node\Expr\BinaryOp;

use SwissKnife202409\PhpParser\Node\Expr\BinaryOp;
class Plus extends BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '+';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_Plus';
    }
}

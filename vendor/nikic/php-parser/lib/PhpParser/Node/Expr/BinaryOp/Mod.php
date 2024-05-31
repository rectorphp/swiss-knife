<?php

declare (strict_types=1);
namespace SwissKnife202405\PhpParser\Node\Expr\BinaryOp;

use SwissKnife202405\PhpParser\Node\Expr\BinaryOp;
class Mod extends BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '%';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_Mod';
    }
}

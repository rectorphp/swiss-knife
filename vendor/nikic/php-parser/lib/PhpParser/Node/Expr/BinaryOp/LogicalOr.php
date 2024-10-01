<?php

declare (strict_types=1);
namespace SwissKnife202410\PhpParser\Node\Expr\BinaryOp;

use SwissKnife202410\PhpParser\Node\Expr\BinaryOp;
class LogicalOr extends BinaryOp
{
    public function getOperatorSigil() : string
    {
        return 'or';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_LogicalOr';
    }
}

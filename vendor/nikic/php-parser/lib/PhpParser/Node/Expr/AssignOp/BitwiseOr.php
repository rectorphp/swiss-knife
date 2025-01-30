<?php

declare (strict_types=1);
namespace SwissKnife202501\PhpParser\Node\Expr\AssignOp;

use SwissKnife202501\PhpParser\Node\Expr\AssignOp;
class BitwiseOr extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_BitwiseOr';
    }
}

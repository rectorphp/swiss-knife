<?php

declare (strict_types=1);
namespace SwissKnife202606\PhpParser\Node\Expr\AssignOp;

use SwissKnife202606\PhpParser\Node\Expr\AssignOp;
class ShiftLeft extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_ShiftLeft';
    }
}

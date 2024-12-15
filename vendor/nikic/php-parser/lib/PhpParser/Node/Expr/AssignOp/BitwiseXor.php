<?php

declare (strict_types=1);
namespace SwissKnife202412\PhpParser\Node\Expr\AssignOp;

use SwissKnife202412\PhpParser\Node\Expr\AssignOp;
class BitwiseXor extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_BitwiseXor';
    }
}

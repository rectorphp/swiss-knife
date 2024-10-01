<?php

declare (strict_types=1);
namespace SwissKnife202410\PhpParser\Node\Expr\AssignOp;

use SwissKnife202410\PhpParser\Node\Expr\AssignOp;
class BitwiseAnd extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_BitwiseAnd';
    }
}

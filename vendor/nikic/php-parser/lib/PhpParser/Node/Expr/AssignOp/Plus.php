<?php

declare (strict_types=1);
namespace SwissKnife202504\PhpParser\Node\Expr\AssignOp;

use SwissKnife202504\PhpParser\Node\Expr\AssignOp;
class Plus extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Plus';
    }
}

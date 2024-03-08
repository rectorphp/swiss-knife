<?php

declare (strict_types=1);
namespace SwissKnife202403\PhpParser\Node\Expr\AssignOp;

use SwissKnife202403\PhpParser\Node\Expr\AssignOp;
class Mod extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Mod';
    }
}

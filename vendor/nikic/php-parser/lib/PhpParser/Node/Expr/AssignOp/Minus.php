<?php

declare (strict_types=1);
namespace SwissKnife202506\PhpParser\Node\Expr\AssignOp;

use SwissKnife202506\PhpParser\Node\Expr\AssignOp;
class Minus extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Minus';
    }
}

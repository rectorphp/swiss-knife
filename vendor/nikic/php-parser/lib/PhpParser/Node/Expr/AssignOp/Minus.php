<?php

declare (strict_types=1);
namespace SwissKnife202409\PhpParser\Node\Expr\AssignOp;

use SwissKnife202409\PhpParser\Node\Expr\AssignOp;
class Minus extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Minus';
    }
}

<?php

declare (strict_types=1);
namespace SwissKnife202402\PhpParser\Node\Expr\AssignOp;

use SwissKnife202402\PhpParser\Node\Expr\AssignOp;
class Pow extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Pow';
    }
}

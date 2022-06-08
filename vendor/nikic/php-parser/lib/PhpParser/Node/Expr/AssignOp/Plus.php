<?php

declare (strict_types=1);
namespace EasyCI20220608\PhpParser\Node\Expr\AssignOp;

use EasyCI20220608\PhpParser\Node\Expr\AssignOp;
class Plus extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Plus';
    }
}

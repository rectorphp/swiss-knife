<?php

declare (strict_types=1);
namespace EasyCI20220417\PhpParser\Node\Expr\AssignOp;

use EasyCI20220417\PhpParser\Node\Expr\AssignOp;
class ShiftRight extends \EasyCI20220417\PhpParser\Node\Expr\AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_ShiftRight';
    }
}

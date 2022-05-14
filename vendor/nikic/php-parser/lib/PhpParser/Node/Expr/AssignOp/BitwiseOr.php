<?php

declare (strict_types=1);
namespace EasyCI20220514\PhpParser\Node\Expr\AssignOp;

use EasyCI20220514\PhpParser\Node\Expr\AssignOp;
class BitwiseOr extends \EasyCI20220514\PhpParser\Node\Expr\AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_BitwiseOr';
    }
}

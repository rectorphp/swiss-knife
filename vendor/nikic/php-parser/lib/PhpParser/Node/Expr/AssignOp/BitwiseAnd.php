<?php

declare (strict_types=1);
namespace EasyCI20220225\PhpParser\Node\Expr\AssignOp;

use EasyCI20220225\PhpParser\Node\Expr\AssignOp;
class BitwiseAnd extends \EasyCI20220225\PhpParser\Node\Expr\AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_BitwiseAnd';
    }
}

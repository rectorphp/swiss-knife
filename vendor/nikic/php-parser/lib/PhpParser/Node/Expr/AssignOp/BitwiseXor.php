<?php

declare (strict_types=1);
namespace EasyCI20220523\PhpParser\Node\Expr\AssignOp;

use EasyCI20220523\PhpParser\Node\Expr\AssignOp;
class BitwiseXor extends \EasyCI20220523\PhpParser\Node\Expr\AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_BitwiseXor';
    }
}

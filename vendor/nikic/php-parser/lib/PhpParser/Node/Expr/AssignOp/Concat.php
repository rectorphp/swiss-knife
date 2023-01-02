<?php

declare (strict_types=1);
namespace EasyCI202301\PhpParser\Node\Expr\AssignOp;

use EasyCI202301\PhpParser\Node\Expr\AssignOp;
class Concat extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Concat';
    }
}

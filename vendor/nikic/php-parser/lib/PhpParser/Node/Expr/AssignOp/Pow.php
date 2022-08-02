<?php

declare (strict_types=1);
namespace EasyCI202208\PhpParser\Node\Expr\AssignOp;

use EasyCI202208\PhpParser\Node\Expr\AssignOp;
class Pow extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Pow';
    }
}

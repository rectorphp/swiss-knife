<?php

declare (strict_types=1);
namespace EasyCI202207\PhpParser\Node\Expr\AssignOp;

use EasyCI202207\PhpParser\Node\Expr\AssignOp;
class Coalesce extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Coalesce';
    }
}

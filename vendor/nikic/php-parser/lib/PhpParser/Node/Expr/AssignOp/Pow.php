<?php

declare (strict_types=1);
namespace EasyCI20220531\PhpParser\Node\Expr\AssignOp;

use EasyCI20220531\PhpParser\Node\Expr\AssignOp;
class Pow extends \EasyCI20220531\PhpParser\Node\Expr\AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Pow';
    }
}

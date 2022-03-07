<?php

declare (strict_types=1);
namespace EasyCI20220307\PhpParser\Node\Expr\AssignOp;

use EasyCI20220307\PhpParser\Node\Expr\AssignOp;
class Minus extends \EasyCI20220307\PhpParser\Node\Expr\AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Minus';
    }
}

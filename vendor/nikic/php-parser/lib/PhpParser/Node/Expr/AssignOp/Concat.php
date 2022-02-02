<?php

declare (strict_types=1);
namespace EasyCI20220202\PhpParser\Node\Expr\AssignOp;

use EasyCI20220202\PhpParser\Node\Expr\AssignOp;
class Concat extends \EasyCI20220202\PhpParser\Node\Expr\AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Concat';
    }
}

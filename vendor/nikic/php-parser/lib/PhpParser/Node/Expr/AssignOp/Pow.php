<?php

declare (strict_types=1);
namespace EasyCI20220527\PhpParser\Node\Expr\AssignOp;

use EasyCI20220527\PhpParser\Node\Expr\AssignOp;
class Pow extends \EasyCI20220527\PhpParser\Node\Expr\AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Pow';
    }
}

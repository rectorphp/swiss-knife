<?php

declare (strict_types=1);
namespace EasyCI20220121\PhpParser\Node\Expr\AssignOp;

use EasyCI20220121\PhpParser\Node\Expr\AssignOp;
class Mod extends \EasyCI20220121\PhpParser\Node\Expr\AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_Mod';
    }
}

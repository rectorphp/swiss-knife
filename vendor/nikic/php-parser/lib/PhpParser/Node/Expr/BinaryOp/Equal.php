<?php

declare (strict_types=1);
namespace EasyCI20220121\PhpParser\Node\Expr\BinaryOp;

use EasyCI20220121\PhpParser\Node\Expr\BinaryOp;
class Equal extends \EasyCI20220121\PhpParser\Node\Expr\BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '==';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_Equal';
    }
}

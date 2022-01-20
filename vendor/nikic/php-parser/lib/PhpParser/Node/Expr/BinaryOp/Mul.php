<?php

declare (strict_types=1);
namespace EasyCI20220120\PhpParser\Node\Expr\BinaryOp;

use EasyCI20220120\PhpParser\Node\Expr\BinaryOp;
class Mul extends \EasyCI20220120\PhpParser\Node\Expr\BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '*';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_Mul';
    }
}
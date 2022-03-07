<?php

declare (strict_types=1);
namespace EasyCI20220307\PhpParser\Node\Expr\BinaryOp;

use EasyCI20220307\PhpParser\Node\Expr\BinaryOp;
class Pow extends \EasyCI20220307\PhpParser\Node\Expr\BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '**';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_Pow';
    }
}

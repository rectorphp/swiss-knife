<?php

declare (strict_types=1);
namespace EasyCI20220116\PhpParser\Node\Expr\BinaryOp;

use EasyCI20220116\PhpParser\Node\Expr\BinaryOp;
class GreaterOrEqual extends \EasyCI20220116\PhpParser\Node\Expr\BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '>=';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_GreaterOrEqual';
    }
}

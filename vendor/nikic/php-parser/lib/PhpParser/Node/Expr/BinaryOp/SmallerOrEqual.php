<?php

declare (strict_types=1);
namespace EasyCI20220225\PhpParser\Node\Expr\BinaryOp;

use EasyCI20220225\PhpParser\Node\Expr\BinaryOp;
class SmallerOrEqual extends \EasyCI20220225\PhpParser\Node\Expr\BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '<=';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_SmallerOrEqual';
    }
}

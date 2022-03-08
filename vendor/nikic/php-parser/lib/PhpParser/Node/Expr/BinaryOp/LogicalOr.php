<?php

declare (strict_types=1);
namespace EasyCI20220308\PhpParser\Node\Expr\BinaryOp;

use EasyCI20220308\PhpParser\Node\Expr\BinaryOp;
class LogicalOr extends \EasyCI20220308\PhpParser\Node\Expr\BinaryOp
{
    public function getOperatorSigil() : string
    {
        return 'or';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_LogicalOr';
    }
}

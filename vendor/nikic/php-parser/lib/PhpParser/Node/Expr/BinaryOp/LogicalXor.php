<?php

declare (strict_types=1);
namespace EasyCI20220521\PhpParser\Node\Expr\BinaryOp;

use EasyCI20220521\PhpParser\Node\Expr\BinaryOp;
class LogicalXor extends \EasyCI20220521\PhpParser\Node\Expr\BinaryOp
{
    public function getOperatorSigil() : string
    {
        return 'xor';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_LogicalXor';
    }
}

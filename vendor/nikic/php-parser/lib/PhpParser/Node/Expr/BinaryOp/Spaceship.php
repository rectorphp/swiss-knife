<?php

declare (strict_types=1);
namespace EasyCI202212\PhpParser\Node\Expr\BinaryOp;

use EasyCI202212\PhpParser\Node\Expr\BinaryOp;
class Spaceship extends BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '<=>';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_Spaceship';
    }
}

<?php

declare (strict_types=1);
namespace EasyCI20220601\PHPStan\PhpDocParser\Ast\Type;

use EasyCI20220601\PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprNode;
use EasyCI20220601\PHPStan\PhpDocParser\Ast\NodeAttributes;
class ConstTypeNode implements \EasyCI20220601\PHPStan\PhpDocParser\Ast\Type\TypeNode
{
    use NodeAttributes;
    /** @var ConstExprNode */
    public $constExpr;
    public function __construct(\EasyCI20220601\PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprNode $constExpr)
    {
        $this->constExpr = $constExpr;
    }
    public function __toString() : string
    {
        return $this->constExpr->__toString();
    }
}

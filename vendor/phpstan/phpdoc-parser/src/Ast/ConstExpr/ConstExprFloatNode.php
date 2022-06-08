<?php

declare (strict_types=1);
namespace EasyCI20220608\PHPStan\PhpDocParser\Ast\ConstExpr;

use EasyCI20220608\PHPStan\PhpDocParser\Ast\NodeAttributes;
class ConstExprFloatNode implements ConstExprNode
{
    use NodeAttributes;
    /** @var string */
    public $value;
    public function __construct(string $value)
    {
        $this->value = $value;
    }
    public function __toString() : string
    {
        return $this->value;
    }
}

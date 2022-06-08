<?php

declare (strict_types=1);
namespace EasyCI20220608\PHPStan\PhpDocParser\Ast\Type;

use EasyCI20220608\PHPStan\PhpDocParser\Ast\NodeAttributes;
class ThisTypeNode implements TypeNode
{
    use NodeAttributes;
    public function __toString() : string
    {
        return '$this';
    }
}

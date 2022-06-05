<?php

declare (strict_types=1);
namespace EasyCI20220605\PHPStan\PhpDocParser\Ast\Type;

use EasyCI20220605\PHPStan\PhpDocParser\Ast\NodeAttributes;
class ThisTypeNode implements \EasyCI20220605\PHPStan\PhpDocParser\Ast\Type\TypeNode
{
    use NodeAttributes;
    public function __toString() : string
    {
        return '$this';
    }
}

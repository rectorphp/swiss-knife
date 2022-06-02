<?php

declare (strict_types=1);
namespace EasyCI20220602\PHPStan\PhpDocParser\Ast\Type;

use EasyCI20220602\PHPStan\PhpDocParser\Ast\NodeAttributes;
class NullableTypeNode implements \EasyCI20220602\PHPStan\PhpDocParser\Ast\Type\TypeNode
{
    use NodeAttributes;
    /** @var TypeNode */
    public $type;
    public function __construct(\EasyCI20220602\PHPStan\PhpDocParser\Ast\Type\TypeNode $type)
    {
        $this->type = $type;
    }
    public function __toString() : string
    {
        return '?' . $this->type;
    }
}

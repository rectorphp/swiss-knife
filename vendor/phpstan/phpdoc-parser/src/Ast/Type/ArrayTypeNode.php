<?php

declare (strict_types=1);
namespace EasyCI20220306\PHPStan\PhpDocParser\Ast\Type;

use EasyCI20220306\PHPStan\PhpDocParser\Ast\NodeAttributes;
class ArrayTypeNode implements \EasyCI20220306\PHPStan\PhpDocParser\Ast\Type\TypeNode
{
    use NodeAttributes;
    /** @var TypeNode */
    public $type;
    public function __construct(\EasyCI20220306\PHPStan\PhpDocParser\Ast\Type\TypeNode $type)
    {
        $this->type = $type;
    }
    public function __toString() : string
    {
        return $this->type . '[]';
    }
}

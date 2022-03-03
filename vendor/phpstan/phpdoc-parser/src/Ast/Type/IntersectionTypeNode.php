<?php

declare (strict_types=1);
namespace EasyCI20220303\PHPStan\PhpDocParser\Ast\Type;

use EasyCI20220303\PHPStan\PhpDocParser\Ast\NodeAttributes;
class IntersectionTypeNode implements \EasyCI20220303\PHPStan\PhpDocParser\Ast\Type\TypeNode
{
    use NodeAttributes;
    /** @var TypeNode[] */
    public $types;
    public function __construct(array $types)
    {
        $this->types = $types;
    }
    public function __toString() : string
    {
        return '(' . \implode(' & ', $this->types) . ')';
    }
}

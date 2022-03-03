<?php

declare (strict_types=1);
namespace EasyCI20220303\PHPStan\PhpDocParser\Ast\Type;

use EasyCI20220303\PHPStan\PhpDocParser\Ast\NodeAttributes;
class IdentifierTypeNode implements \EasyCI20220303\PHPStan\PhpDocParser\Ast\Type\TypeNode
{
    use NodeAttributes;
    /** @var string */
    public $name;
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    public function __toString() : string
    {
        return $this->name;
    }
}

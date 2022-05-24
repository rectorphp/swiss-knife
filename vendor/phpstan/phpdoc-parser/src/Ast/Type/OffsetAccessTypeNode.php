<?php

declare (strict_types=1);
namespace EasyCI20220524\PHPStan\PhpDocParser\Ast\Type;

use EasyCI20220524\PHPStan\PhpDocParser\Ast\NodeAttributes;
class OffsetAccessTypeNode implements \EasyCI20220524\PHPStan\PhpDocParser\Ast\Type\TypeNode
{
    use NodeAttributes;
    /** @var TypeNode */
    public $type;
    /** @var TypeNode */
    public $offset;
    public function __construct(\EasyCI20220524\PHPStan\PhpDocParser\Ast\Type\TypeNode $type, \EasyCI20220524\PHPStan\PhpDocParser\Ast\Type\TypeNode $offset)
    {
        $this->type = $type;
        $this->offset = $offset;
    }
    public function __toString() : string
    {
        return $this->type . '[' . $this->offset . ']';
    }
}

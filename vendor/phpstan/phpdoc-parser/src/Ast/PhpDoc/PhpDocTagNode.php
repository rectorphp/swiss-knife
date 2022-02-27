<?php

declare (strict_types=1);
namespace EasyCI20220227\PHPStan\PhpDocParser\Ast\PhpDoc;

use EasyCI20220227\PHPStan\PhpDocParser\Ast\NodeAttributes;
class PhpDocTagNode implements \EasyCI20220227\PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocChildNode
{
    use NodeAttributes;
    /** @var string */
    public $name;
    /** @var PhpDocTagValueNode */
    public $value;
    public function __construct(string $name, \EasyCI20220227\PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode $value)
    {
        $this->name = $name;
        $this->value = $value;
    }
    public function __toString() : string
    {
        return \trim("{$this->name} {$this->value}");
    }
}

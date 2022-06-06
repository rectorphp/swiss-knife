<?php

declare (strict_types=1);
namespace EasyCI20220606\PHPStan\PhpDocParser\Ast\PhpDoc;

use EasyCI20220606\PHPStan\PhpDocParser\Ast\NodeAttributes;
use function trim;
class PhpDocTagNode implements \EasyCI20220606\PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocChildNode
{
    use NodeAttributes;
    /** @var string */
    public $name;
    /** @var PhpDocTagValueNode */
    public $value;
    public function __construct(string $name, \EasyCI20220606\PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode $value)
    {
        $this->name = $name;
        $this->value = $value;
    }
    public function __toString() : string
    {
        return \trim("{$this->name} {$this->value}");
    }
}

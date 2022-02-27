<?php

declare (strict_types=1);
namespace EasyCI20220227\PHPStan\PhpDocParser\Ast\PhpDoc;

use EasyCI20220227\PHPStan\PhpDocParser\Ast\NodeAttributes;
class DeprecatedTagValueNode implements \EasyCI20220227\PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode
{
    use NodeAttributes;
    /** @var string (may be empty) */
    public $description;
    public function __construct(string $description)
    {
        $this->description = $description;
    }
    public function __toString() : string
    {
        return \trim($this->description);
    }
}

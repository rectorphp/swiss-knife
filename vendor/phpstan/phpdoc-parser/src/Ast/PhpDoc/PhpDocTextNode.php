<?php

declare (strict_types=1);
namespace EasyCI20220414\PHPStan\PhpDocParser\Ast\PhpDoc;

use EasyCI20220414\PHPStan\PhpDocParser\Ast\NodeAttributes;
class PhpDocTextNode implements \EasyCI20220414\PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocChildNode
{
    use NodeAttributes;
    /** @var string */
    public $text;
    public function __construct(string $text)
    {
        $this->text = $text;
    }
    public function __toString() : string
    {
        return $this->text;
    }
}

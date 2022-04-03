<?php

declare (strict_types=1);
namespace EasyCI20220403\PHPStan\PhpDocParser\Ast\PhpDoc;

use EasyCI20220403\PHPStan\PhpDocParser\Ast\NodeAttributes;
use EasyCI20220403\PHPStan\PhpDocParser\Parser\ParserException;
class InvalidTagValueNode implements \EasyCI20220403\PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode
{
    use NodeAttributes;
    /** @var string (may be empty) */
    public $value;
    /** @var ParserException */
    public $exception;
    public function __construct(string $value, \EasyCI20220403\PHPStan\PhpDocParser\Parser\ParserException $exception)
    {
        $this->value = $value;
        $this->exception = $exception;
    }
    public function __toString() : string
    {
        return $this->value;
    }
}

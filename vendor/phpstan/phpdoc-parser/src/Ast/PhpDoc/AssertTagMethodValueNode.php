<?php

declare (strict_types=1);
namespace EasyCI202208\PHPStan\PhpDocParser\Ast\PhpDoc;

use EasyCI202208\PHPStan\PhpDocParser\Ast\NodeAttributes;
use EasyCI202208\PHPStan\PhpDocParser\Ast\Type\TypeNode;
use function trim;
class AssertTagMethodValueNode implements PhpDocTagValueNode
{
    use NodeAttributes;
    /** @var TypeNode */
    public $type;
    /** @var string */
    public $parameter;
    /** @var string */
    public $method;
    /** @var bool */
    public $isNegated;
    /** @var string (may be empty) */
    public $description;
    public function __construct(TypeNode $type, string $parameter, string $method, bool $isNegated, string $description)
    {
        $this->type = $type;
        $this->parameter = $parameter;
        $this->method = $method;
        $this->isNegated = $isNegated;
        $this->description = $description;
    }
    public function __toString() : string
    {
        $isNegated = $this->isNegated ? '!' : '';
        return trim("{$this->type} {$isNegated}{$this->parameter}->{$this->method}() {$this->description}");
    }
}

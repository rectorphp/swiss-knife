<?php

declare (strict_types=1);
namespace EasyCI20220325\PHPStan\PhpDocParser\Ast\PhpDoc;

use EasyCI20220325\PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprNode;
use EasyCI20220325\PHPStan\PhpDocParser\Ast\Node;
use EasyCI20220325\PHPStan\PhpDocParser\Ast\NodeAttributes;
use EasyCI20220325\PHPStan\PhpDocParser\Ast\Type\TypeNode;
class MethodTagValueParameterNode implements \EasyCI20220325\PHPStan\PhpDocParser\Ast\Node
{
    use NodeAttributes;
    /** @var TypeNode|null */
    public $type;
    /** @var bool */
    public $isReference;
    /** @var bool */
    public $isVariadic;
    /** @var string */
    public $parameterName;
    /** @var ConstExprNode|null */
    public $defaultValue;
    public function __construct(?\EasyCI20220325\PHPStan\PhpDocParser\Ast\Type\TypeNode $type, bool $isReference, bool $isVariadic, string $parameterName, ?\EasyCI20220325\PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprNode $defaultValue)
    {
        $this->type = $type;
        $this->isReference = $isReference;
        $this->isVariadic = $isVariadic;
        $this->parameterName = $parameterName;
        $this->defaultValue = $defaultValue;
    }
    public function __toString() : string
    {
        $type = $this->type !== null ? "{$this->type} " : '';
        $isReference = $this->isReference ? '&' : '';
        $isVariadic = $this->isVariadic ? '...' : '';
        $default = $this->defaultValue !== null ? " = {$this->defaultValue}" : '';
        return "{$type}{$isReference}{$isVariadic}{$this->parameterName}{$default}";
    }
}

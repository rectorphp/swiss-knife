<?php

declare (strict_types=1);
namespace EasyCI20220418\PHPStan\PhpDocParser\Ast\Type;

use EasyCI20220418\PHPStan\PhpDocParser\Ast\NodeAttributes;
use function sprintf;
class ConditionalTypeForParameterNode implements \EasyCI20220418\PHPStan\PhpDocParser\Ast\Type\TypeNode
{
    use NodeAttributes;
    /** @var string */
    public $parameterName;
    /** @var TypeNode */
    public $targetType;
    /** @var TypeNode */
    public $if;
    /** @var TypeNode */
    public $else;
    /** @var bool */
    public $negated;
    public function __construct(string $parameterName, \EasyCI20220418\PHPStan\PhpDocParser\Ast\Type\TypeNode $targetType, \EasyCI20220418\PHPStan\PhpDocParser\Ast\Type\TypeNode $if, \EasyCI20220418\PHPStan\PhpDocParser\Ast\Type\TypeNode $else, bool $negated)
    {
        $this->parameterName = $parameterName;
        $this->targetType = $targetType;
        $this->if = $if;
        $this->else = $else;
        $this->negated = $negated;
    }
    public function __toString() : string
    {
        return \sprintf('(%s %s %s ? %s : %s)', $this->parameterName, $this->negated ? 'is not' : 'is', $this->targetType, $this->if, $this->else);
    }
}

<?php

declare (strict_types=1);
namespace EasyCI20220416\Symplify\Astral\PhpDocParser\ValueObject\Ast\PhpDoc;

use EasyCI20220416\PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use EasyCI20220416\PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use EasyCI20220416\PHPStan\PhpDocParser\Ast\Type\TypeNode;
/**
 * @noRector final on purpose, so it can be extended by 3rd party
 */
class SimplePhpDocNode extends \EasyCI20220416\PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode
{
    public function getParam(string $desiredParamName) : ?\EasyCI20220416\PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode
    {
        $desiredParamNameWithDollar = '$' . \ltrim($desiredParamName, '$');
        foreach ($this->getParamTagValues() as $paramTagValueNode) {
            if ($paramTagValueNode->parameterName !== $desiredParamNameWithDollar) {
                continue;
            }
            return $paramTagValueNode;
        }
        return null;
    }
    public function getParamType(string $desiredParamName) : ?\EasyCI20220416\PHPStan\PhpDocParser\Ast\Type\TypeNode
    {
        $paramTagValueNode = $this->getParam($desiredParamName);
        if (!$paramTagValueNode instanceof \EasyCI20220416\PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode) {
            return null;
        }
        return $paramTagValueNode->type;
    }
}

<?php

declare (strict_types=1);
namespace EasyCI20220612\Symplify\Astral\TypeAnalyzer;

use EasyCI20220612\PhpParser\Node\Stmt\ClassMethod;
use EasyCI20220612\PHPStan\Analyser\Scope;
use EasyCI20220612\PHPStan\Reflection\ClassReflection;
use EasyCI20220612\PHPStan\Reflection\FunctionVariant;
use EasyCI20220612\PHPStan\Reflection\ParametersAcceptorSelector;
use EasyCI20220612\PHPStan\Type\MixedType;
use EasyCI20220612\PHPStan\Type\Type;
use EasyCI20220612\Symplify\Astral\Exception\ShouldNotHappenException;
use EasyCI20220612\Symplify\Astral\Naming\SimpleNameResolver;
/**
 * @api
 */
final class ClassMethodReturnTypeResolver
{
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    public function __construct(SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
    }
    public function resolve(ClassMethod $classMethod, Scope $scope) : Type
    {
        $methodName = $this->simpleNameResolver->getName($classMethod);
        if (!\is_string($methodName)) {
            throw new ShouldNotHappenException();
        }
        $classReflection = $scope->getClassReflection();
        if (!$classReflection instanceof ClassReflection) {
            return new MixedType();
        }
        $extendedMethodReflection = $classReflection->getMethod($methodName, $scope);
        $functionVariant = ParametersAcceptorSelector::selectSingle($extendedMethodReflection->getVariants());
        if (!$functionVariant instanceof FunctionVariant) {
            return new MixedType();
        }
        return $functionVariant->getReturnType();
    }
}

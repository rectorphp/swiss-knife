<?php

declare (strict_types=1);
namespace EasyCI202208\Symplify\Astral\TypeAnalyzer;

use EasyCI202208\PhpParser\Node\Stmt\ClassMethod;
use EasyCI202208\PHPStan\Analyser\Scope;
use EasyCI202208\PHPStan\Reflection\ClassReflection;
use EasyCI202208\PHPStan\Reflection\FunctionVariant;
use EasyCI202208\PHPStan\Reflection\ParametersAcceptorSelector;
use EasyCI202208\PHPStan\Type\MixedType;
use EasyCI202208\PHPStan\Type\Type;
/**
 * @api
 */
final class ClassMethodReturnTypeResolver
{
    public function resolve(ClassMethod $classMethod, Scope $scope) : Type
    {
        $methodName = $classMethod->name->toString();
        $classReflection = $scope->getClassReflection();
        if (!$classReflection instanceof ClassReflection) {
            return new MixedType();
        }
        $extendedMethodReflection = $classReflection->getMethod($methodName, $scope);
        $parametersAcceptor = ParametersAcceptorSelector::selectSingle($extendedMethodReflection->getVariants());
        if (!$parametersAcceptor instanceof FunctionVariant) {
            return new MixedType();
        }
        return $parametersAcceptor->getReturnType();
    }
}

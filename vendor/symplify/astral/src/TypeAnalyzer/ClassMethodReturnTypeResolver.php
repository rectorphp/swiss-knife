<?php

declare (strict_types=1);
namespace EasyCI202207\Symplify\Astral\TypeAnalyzer;

use EasyCI202207\PhpParser\Node\Stmt\ClassMethod;
use EasyCI202207\PHPStan\Analyser\Scope;
use EasyCI202207\PHPStan\Reflection\ClassReflection;
use EasyCI202207\PHPStan\Reflection\FunctionVariant;
use EasyCI202207\PHPStan\Reflection\ParametersAcceptorSelector;
use EasyCI202207\PHPStan\Type\MixedType;
use EasyCI202207\PHPStan\Type\Type;
use EasyCI202207\Symplify\Astral\Exception\ShouldNotHappenException;
use EasyCI202207\Symplify\Astral\Naming\SimpleNameResolver;
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
        $parametersAcceptor = ParametersAcceptorSelector::selectSingle($extendedMethodReflection->getVariants());
        if (!$parametersAcceptor instanceof FunctionVariant) {
            return new MixedType();
        }
        return $parametersAcceptor->getReturnType();
    }
}

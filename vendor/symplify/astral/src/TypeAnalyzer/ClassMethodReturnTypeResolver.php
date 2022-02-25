<?php

declare (strict_types=1);
namespace EasyCI20220225\Symplify\Astral\TypeAnalyzer;

use EasyCI20220225\PhpParser\Node\Stmt\ClassMethod;
use EasyCI20220225\PHPStan\Analyser\Scope;
use EasyCI20220225\PHPStan\Reflection\ClassReflection;
use EasyCI20220225\PHPStan\Reflection\FunctionVariant;
use EasyCI20220225\PHPStan\Reflection\ParametersAcceptorSelector;
use EasyCI20220225\PHPStan\Type\MixedType;
use EasyCI20220225\PHPStan\Type\Type;
use EasyCI20220225\Symplify\Astral\Exception\ShouldNotHappenException;
use EasyCI20220225\Symplify\Astral\Naming\SimpleNameResolver;
/**
 * @api
 */
final class ClassMethodReturnTypeResolver
{
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    public function __construct(\EasyCI20220225\Symplify\Astral\Naming\SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
    }
    public function resolve(\EasyCI20220225\PhpParser\Node\Stmt\ClassMethod $classMethod, \EasyCI20220225\PHPStan\Analyser\Scope $scope) : \EasyCI20220225\PHPStan\Type\Type
    {
        $methodName = $this->simpleNameResolver->getName($classMethod);
        if (!\is_string($methodName)) {
            throw new \EasyCI20220225\Symplify\Astral\Exception\ShouldNotHappenException();
        }
        $classReflection = $scope->getClassReflection();
        if (!$classReflection instanceof \EasyCI20220225\PHPStan\Reflection\ClassReflection) {
            return new \EasyCI20220225\PHPStan\Type\MixedType();
        }
        $methodReflection = $classReflection->getMethod($methodName, $scope);
        $functionVariant = \EasyCI20220225\PHPStan\Reflection\ParametersAcceptorSelector::selectSingle($methodReflection->getVariants());
        if (!$functionVariant instanceof \EasyCI20220225\PHPStan\Reflection\FunctionVariant) {
            return new \EasyCI20220225\PHPStan\Type\MixedType();
        }
        return $functionVariant->getReturnType();
    }
}

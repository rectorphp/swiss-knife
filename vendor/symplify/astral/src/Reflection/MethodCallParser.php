<?php

declare (strict_types=1);
namespace EasyCI20220529\Symplify\Astral\Reflection;

use EasyCI20220529\PhpParser\Node\Expr\MethodCall;
use EasyCI20220529\PhpParser\Node\Stmt\ClassMethod;
use EasyCI20220529\PHPStan\Analyser\Scope;
use EasyCI20220529\PHPStan\Reflection\ClassReflection;
use EasyCI20220529\PHPStan\Type\ObjectType;
use EasyCI20220529\PHPStan\Type\ThisType;
use EasyCI20220529\Symplify\Astral\Naming\SimpleNameResolver;
/**
 * @api
 */
final class MethodCallParser
{
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    /**
     * @var \Symplify\Astral\Reflection\ReflectionParser
     */
    private $reflectionParser;
    public function __construct(\EasyCI20220529\Symplify\Astral\Naming\SimpleNameResolver $simpleNameResolver, \EasyCI20220529\Symplify\Astral\Reflection\ReflectionParser $reflectionParser)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->reflectionParser = $reflectionParser;
    }
    /**
     * @return \PhpParser\Node\Stmt\ClassMethod|null
     */
    public function parseMethodCall(\EasyCI20220529\PhpParser\Node\Expr\MethodCall $methodCall, \EasyCI20220529\PHPStan\Analyser\Scope $scope)
    {
        $callerType = $scope->getType($methodCall->var);
        if ($callerType instanceof \EasyCI20220529\PHPStan\Type\ThisType) {
            $callerType = $callerType->getStaticObjectType();
        }
        if (!$callerType instanceof \EasyCI20220529\PHPStan\Type\ObjectType) {
            return null;
        }
        $classReflection = $callerType->getClassReflection();
        if (!$classReflection instanceof \EasyCI20220529\PHPStan\Reflection\ClassReflection) {
            return null;
        }
        $methodName = $this->simpleNameResolver->getName($methodCall->name);
        if ($methodName === null) {
            return null;
        }
        if (!$classReflection->hasNativeMethod($methodName)) {
            return null;
        }
        $methodReflection = $classReflection->getNativeMethod($methodName);
        return $this->reflectionParser->parsePHPStanMethodReflection($methodReflection);
    }
}

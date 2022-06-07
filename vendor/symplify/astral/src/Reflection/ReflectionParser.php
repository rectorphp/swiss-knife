<?php

declare (strict_types=1);
namespace EasyCI20220607\Symplify\Astral\Reflection;

use EasyCI20220607\PhpParser\Node;
use EasyCI20220607\PhpParser\Node\Stmt\Class_;
use EasyCI20220607\PhpParser\Node\Stmt\ClassMethod;
use EasyCI20220607\PhpParser\Node\Stmt\Property;
use EasyCI20220607\PhpParser\NodeFinder;
use EasyCI20220607\PHPStan\Reflection\MethodReflection;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use EasyCI20220607\Symplify\Astral\PhpParser\SmartPhpParser;
use Throwable;
/**
 * @api
 */
final class ReflectionParser
{
    /**
     * @var \Symplify\Astral\PhpParser\SmartPhpParser
     */
    private $smartPhpParser;
    /**
     * @var \PhpParser\NodeFinder
     */
    private $nodeFinder;
    public function __construct(\EasyCI20220607\Symplify\Astral\PhpParser\SmartPhpParser $smartPhpParser, \EasyCI20220607\PhpParser\NodeFinder $nodeFinder)
    {
        $this->smartPhpParser = $smartPhpParser;
        $this->nodeFinder = $nodeFinder;
    }
    public function parsePHPStanMethodReflection(\EasyCI20220607\PHPStan\Reflection\MethodReflection $methodReflection) : ?\EasyCI20220607\PhpParser\Node\Stmt\ClassMethod
    {
        $classReflection = $methodReflection->getDeclaringClass();
        $fileName = $classReflection->getFileName();
        if ($fileName === null) {
            return null;
        }
        $class = $this->parseFilenameToClass($fileName);
        if (!$class instanceof \EasyCI20220607\PhpParser\Node) {
            return null;
        }
        return $class->getMethod($methodReflection->getName());
    }
    public function parseMethodReflection(\ReflectionMethod $reflectionMethod) : ?\EasyCI20220607\PhpParser\Node\Stmt\ClassMethod
    {
        $class = $this->parseNativeClassReflection($reflectionMethod->getDeclaringClass());
        if (!$class instanceof \EasyCI20220607\PhpParser\Node\Stmt\Class_) {
            return null;
        }
        return $class->getMethod($reflectionMethod->getName());
    }
    public function parsePropertyReflection(\ReflectionProperty $reflectionProperty) : ?\EasyCI20220607\PhpParser\Node\Stmt\Property
    {
        $class = $this->parseNativeClassReflection($reflectionProperty->getDeclaringClass());
        if (!$class instanceof \EasyCI20220607\PhpParser\Node\Stmt\Class_) {
            return null;
        }
        return $class->getProperty($reflectionProperty->getName());
    }
    private function parseNativeClassReflection(\ReflectionClass $reflectionClass) : ?\EasyCI20220607\PhpParser\Node\Stmt\Class_
    {
        $fileName = $reflectionClass->getFileName();
        if ($fileName === \false) {
            return null;
        }
        return $this->parseFilenameToClass($fileName);
    }
    /**
     * @return \PhpParser\Node\Stmt\Class_|null
     */
    private function parseFilenameToClass(string $fileName)
    {
        try {
            $stmts = $this->smartPhpParser->parseFile($fileName);
        } catch (\Throwable $exception) {
            // not reachable
            return null;
        }
        $class = $this->nodeFinder->findFirstInstanceOf($stmts, \EasyCI20220607\PhpParser\Node\Stmt\Class_::class);
        if (!$class instanceof \EasyCI20220607\PhpParser\Node\Stmt\Class_) {
            return null;
        }
        return $class;
    }
}

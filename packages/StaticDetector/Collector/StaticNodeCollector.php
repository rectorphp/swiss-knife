<?php

declare (strict_types=1);
namespace Symplify\EasyCI\StaticDetector\Collector;

use EasyCI20220606\PhpParser\Node\Expr;
use EasyCI20220606\PhpParser\Node\Expr\StaticCall;
use EasyCI20220606\PhpParser\Node\Name;
use EasyCI20220606\PhpParser\Node\Stmt\Class_;
use EasyCI20220606\PhpParser\Node\Stmt\ClassLike;
use EasyCI20220606\PhpParser\Node\Stmt\ClassMethod;
use EasyCI20220606\Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\EasyCI\StaticDetector\ValueObject\StaticClassMethod;
use Symplify\EasyCI\StaticDetector\ValueObject\StaticClassMethodWithStaticCalls;
use Symplify\EasyCI\StaticDetector\ValueObject\StaticReport;
use EasyCI20220606\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
final class StaticNodeCollector
{
    /**
     * @var StaticClassMethod[]
     */
    private $staticClassMethods = [];
    /**
     * @var array<string, array<string, StaticCall[]>>
     */
    private $staticCalls = [];
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    public function __construct(\EasyCI20220606\Symplify\Astral\Naming\SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
    }
    public function addStaticClassMethod(\EasyCI20220606\PhpParser\Node\Stmt\ClassMethod $classMethod, \EasyCI20220606\PhpParser\Node\Stmt\ClassLike $classLike) : void
    {
        $className = $this->simpleNameResolver->getName($classLike);
        if ($className === null) {
            return;
        }
        $methodName = (string) $classMethod->name;
        $this->staticClassMethods[] = new \Symplify\EasyCI\StaticDetector\ValueObject\StaticClassMethod($className, $methodName, $classMethod);
    }
    public function addStaticCall(\EasyCI20220606\PhpParser\Node\Expr\StaticCall $staticCall) : void
    {
        if ($staticCall->class instanceof \EasyCI20220606\PhpParser\Node\Expr) {
            // weird expression, skip
            return;
        }
        if ($staticCall->name instanceof \EasyCI20220606\PhpParser\Node\Expr) {
            // weird expression, skip
            return;
        }
        $class = (string) $staticCall->class;
        $method = (string) $staticCall->name;
        $this->staticCalls[$class][$method][] = $staticCall;
    }
    public function addStaticCallInsideClass(\EasyCI20220606\PhpParser\Node\Expr\StaticCall $staticCall, \EasyCI20220606\PhpParser\Node\Stmt\ClassLike $classLike) : void
    {
        if ($staticCall->class instanceof \EasyCI20220606\PhpParser\Node\Expr) {
            // weird expression, skip
            return;
        }
        if ($staticCall->name instanceof \EasyCI20220606\PhpParser\Node\Expr) {
            // weird expression, skip
            return;
        }
        $class = $this->resolveClass($staticCall->class, $classLike);
        $method = (string) $staticCall->name;
        $this->staticCalls[$class][$method][] = $staticCall;
    }
    public function generateStaticReport() : \Symplify\EasyCI\StaticDetector\ValueObject\StaticReport
    {
        return new \Symplify\EasyCI\StaticDetector\ValueObject\StaticReport($this->getStaticClassMethodWithStaticCalls());
    }
    /**
     * @return StaticClassMethodWithStaticCalls[]
     */
    private function getStaticClassMethodWithStaticCalls() : array
    {
        $staticClassMethodWithStaticCalls = [];
        foreach ($this->staticClassMethods as $staticClassMethod) {
            $staticCalls = $this->staticCalls[$staticClassMethod->getClass()][$staticClassMethod->getMethod()] ?? [];
            $staticClassMethodWithStaticCalls[] = new \Symplify\EasyCI\StaticDetector\ValueObject\StaticClassMethodWithStaticCalls($staticClassMethod, $staticCalls);
        }
        return $staticClassMethodWithStaticCalls;
    }
    private function resolveClass(\EasyCI20220606\PhpParser\Node\Name $staticClassName, \EasyCI20220606\PhpParser\Node\Stmt\ClassLike $classLike) : string
    {
        $class = (string) $staticClassName;
        if (\in_array($class, ['self', 'static'], \true)) {
            return (string) $this->simpleNameResolver->getName($classLike);
        }
        if ($class === 'parent') {
            if (!$classLike instanceof \EasyCI20220606\PhpParser\Node\Stmt\Class_) {
                throw new \EasyCI20220606\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
            }
            if ($classLike->extends === null) {
                throw new \EasyCI20220606\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
            }
            return (string) $classLike->extends;
        }
        return $class;
    }
}

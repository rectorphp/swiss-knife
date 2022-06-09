<?php

declare (strict_types=1);
namespace Symplify\EasyCI\StaticDetector\NodeVisitor;

use EasyCI20220609\PhpParser\Node;
use EasyCI20220609\PhpParser\Node\Expr\StaticCall;
use EasyCI20220609\PhpParser\Node\Stmt\ClassLike;
use EasyCI20220609\PhpParser\Node\Stmt\ClassMethod;
use EasyCI20220609\PhpParser\NodeVisitorAbstract;
use EasyCI20220609\Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\EasyCI\StaticDetector\Collector\StaticNodeCollector;
use EasyCI20220609\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
final class StaticCollectNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private const ALLOWED_METHOD_NAMES = ['getSubscribedEvents'];
    /**
     * @var \PhpParser\Node\Stmt\ClassLike|null
     */
    private $currentClassLike;
    /**
     * @var \Symplify\EasyCI\StaticDetector\Collector\StaticNodeCollector
     */
    private $staticNodeCollector;
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    public function __construct(StaticNodeCollector $staticNodeCollector, SimpleNameResolver $simpleNameResolver)
    {
        $this->staticNodeCollector = $staticNodeCollector;
        $this->simpleNameResolver = $simpleNameResolver;
    }
    public function enterNode(Node $node)
    {
        $this->ensureClassLikeOrStaticCall($node);
        if ($node instanceof ClassMethod) {
            $this->enterClassMethod($node);
        }
        return null;
    }
    private function ensureClassLikeOrStaticCall(Node $node) : void
    {
        if ($node instanceof ClassLike) {
            $this->currentClassLike = $node;
        }
        if ($node instanceof StaticCall) {
            if ($this->currentClassLike !== null) {
                $this->staticNodeCollector->addStaticCallInsideClass($node, $this->currentClassLike);
            } else {
                $this->staticNodeCollector->addStaticCall($node);
            }
        }
    }
    private function enterClassMethod(ClassMethod $classMethod) : void
    {
        if (!$classMethod->isStatic()) {
            return;
        }
        $classMethodName = (string) $classMethod->name;
        if (\in_array($classMethodName, self::ALLOWED_METHOD_NAMES, \true)) {
            return;
        }
        if ($this->currentClassLike === null) {
            $errorMessage = \sprintf('Class not found for static call "%s"', $classMethodName);
            throw new ShouldNotHappenException($errorMessage);
        }
        $currentClassName = $this->simpleNameResolver->getName($this->currentClassLike);
        if ($currentClassName === null) {
            return;
        }
        $this->staticNodeCollector->addStaticClassMethod($classMethod, $this->currentClassLike);
    }
}

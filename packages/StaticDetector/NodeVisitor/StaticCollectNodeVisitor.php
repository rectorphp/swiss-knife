<?php

declare (strict_types=1);
namespace Symplify\EasyCI\StaticDetector\NodeVisitor;

use EasyCI20220117\PhpParser\Node;
use EasyCI20220117\PhpParser\Node\Expr\StaticCall;
use EasyCI20220117\PhpParser\Node\Stmt\ClassLike;
use EasyCI20220117\PhpParser\Node\Stmt\ClassMethod;
use EasyCI20220117\PhpParser\NodeVisitorAbstract;
use EasyCI20220117\Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\EasyCI\StaticDetector\Collector\StaticNodeCollector;
use EasyCI20220117\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
final class StaticCollectNodeVisitor extends \EasyCI20220117\PhpParser\NodeVisitorAbstract
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
    public function __construct(\Symplify\EasyCI\StaticDetector\Collector\StaticNodeCollector $staticNodeCollector, \EasyCI20220117\Symplify\Astral\Naming\SimpleNameResolver $simpleNameResolver)
    {
        $this->staticNodeCollector = $staticNodeCollector;
        $this->simpleNameResolver = $simpleNameResolver;
    }
    public function enterNode(\EasyCI20220117\PhpParser\Node $node)
    {
        $this->ensureClassLikeOrStaticCall($node);
        if ($node instanceof \EasyCI20220117\PhpParser\Node\Stmt\ClassMethod) {
            $this->enterClassMethod($node);
        }
        return null;
    }
    private function ensureClassLikeOrStaticCall(\EasyCI20220117\PhpParser\Node $node) : void
    {
        if ($node instanceof \EasyCI20220117\PhpParser\Node\Stmt\ClassLike) {
            $this->currentClassLike = $node;
        }
        if ($node instanceof \EasyCI20220117\PhpParser\Node\Expr\StaticCall) {
            if ($this->currentClassLike !== null) {
                $this->staticNodeCollector->addStaticCallInsideClass($node, $this->currentClassLike);
            } else {
                $this->staticNodeCollector->addStaticCall($node);
            }
        }
    }
    private function enterClassMethod(\EasyCI20220117\PhpParser\Node\Stmt\ClassMethod $classMethod) : void
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
            throw new \EasyCI20220117\Symplify\SymplifyKernel\Exception\ShouldNotHappenException($errorMessage);
        }
        $currentClassName = $this->simpleNameResolver->getName($this->currentClassLike);
        if ($currentClassName === null) {
            return;
        }
        $this->staticNodeCollector->addStaticClassMethod($classMethod, $this->currentClassLike);
    }
}

<?php

declare (strict_types=1);
namespace Rector\SwissKnife\PhpParser\NodeVisitor;

use SwissKnife202409\PhpParser\Node;
use SwissKnife202409\PhpParser\Node\Expr;
use SwissKnife202409\PhpParser\Node\Expr\ClassConstFetch;
use SwissKnife202409\PhpParser\Node\Name;
use SwissKnife202409\PhpParser\Node\Stmt\Class_;
use SwissKnife202409\PhpParser\Node\Stmt\Enum_;
use SwissKnife202409\PhpParser\Node\Stmt\Interface_;
use SwissKnife202409\PhpParser\Node\Stmt\Trait_;
use SwissKnife202409\PhpParser\NodeTraverser;
use SwissKnife202409\PhpParser\NodeVisitorAbstract;
use Rector\SwissKnife\Contract\ClassConstantFetchInterface;
use Rector\SwissKnife\Enum\StaticAccessor;
use Rector\SwissKnife\Exception\NotImplementedYetException;
use Rector\SwissKnife\Exception\ShouldNotHappenException;
use Rector\SwissKnife\ValueObject\ClassConstantFetch\CurrentClassConstantFetch;
use Rector\SwissKnife\ValueObject\ClassConstantFetch\ExternalClassAccessConstantFetch;
use Rector\SwissKnife\ValueObject\ClassConstantFetch\ParentClassConstantFetch;
use Rector\SwissKnife\ValueObject\ClassConstantFetch\StaticClassConstantFetch;
use ReflectionClass;
use SwissKnife202409\Webmozart\Assert\Assert;
final class FindClassConstFetchNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var \PhpParser\Node\Stmt\Class_|null
     */
    private $currentClass;
    /**
     * @var ClassConstantFetchInterface[]
     */
    private $classConstantFetches = [];
    /**
     * @return \PhpParser\Node|int|null
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Interface_ || $node instanceof Trait_ || $node instanceof Enum_) {
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }
        if ($node instanceof Class_) {
            // skip anonymous classes as problematic
            if ($node->isAnonymous()) {
                return NodeTraverser::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
            }
            $this->currentClass = $node;
            return null;
        }
        if (!$node instanceof ClassConstFetch) {
            return null;
        }
        // unable to resolve → skip
        if (!$node->class instanceof Name) {
            return null;
        }
        $className = $node->class->toString();
        if ($node->name instanceof Expr) {
            // unable to resolve → skip
            return null;
        }
        $constantName = $node->name->toString();
        // always public magic
        if ($constantName === 'class') {
            return null;
        }
        if ($className === StaticAccessor::SELF) {
            Assert::isInstanceOf($this->currentClass, Class_::class);
            $currentClassName = $this->getClassName();
            if ($this->isCurrentClassConstant($this->currentClass, $constantName)) {
                $this->classConstantFetches[] = new CurrentClassConstantFetch($currentClassName, $constantName);
                return $node;
            }
            // check if parent class is vendor
            if ($this->currentClass->extends instanceof Name) {
                $parentClassName = $this->currentClass->extends->toString();
                if ($this->isVendorClassName($parentClassName)) {
                    return null;
                }
            }
            $this->classConstantFetches[] = new ParentClassConstantFetch($currentClassName, $constantName);
            return $node;
        }
        if ($className === StaticAccessor::STATIC) {
            Assert::isInstanceOf($this->currentClass, Class_::class);
            $currentClassName = $this->getClassName();
            if ($this->isCurrentClassConstant($this->currentClass, $constantName)) {
                $this->classConstantFetches[] = new CurrentClassConstantFetch($currentClassName, $constantName);
                return $node;
            }
            $this->classConstantFetches[] = new StaticClassConstantFetch($currentClassName, $constantName);
            return $node;
        }
        if ($this->doesClassExist($className)) {
            // is class from /vendor? we can skip it
            if ($this->isVendorClassName($className)) {
                return null;
            }
            // is vendor fetch? skip
            $this->classConstantFetches[] = new ExternalClassAccessConstantFetch($className, $constantName);
            return null;
        }
        throw new NotImplementedYetException(\sprintf('Class "%s" was not found', $className));
    }
    public function leaveNode(Node $node) : ?Node
    {
        if (!$node instanceof Class_) {
            return null;
        }
        // we've left class, lets reset its value
        $this->currentClass = null;
        return $node;
    }
    /**
     * @return ClassConstantFetchInterface[]
     */
    public function getClassConstantFetches() : array
    {
        return $this->classConstantFetches;
    }
    private function isVendorClassName(string $className) : bool
    {
        if (!$this->doesClassExist($className)) {
            throw new ShouldNotHappenException(\sprintf('Class "%s" could not be found', $className));
        }
        $reflectionClass = new ReflectionClass($className);
        return \strpos((string) $reflectionClass->getFileName(), 'vendor') !== \false;
    }
    private function isCurrentClassConstant(Class_ $currentClass, string $constantName) : bool
    {
        foreach ($currentClass->getConstants() as $classConstant) {
            foreach ($classConstant->consts as $const) {
                if ($const->name->toString() === $constantName) {
                    return \true;
                }
            }
        }
        return \false;
    }
    private function getClassName() : string
    {
        if (!$this->currentClass instanceof Class_) {
            throw new ShouldNotHappenException('Class_ node is missing');
        }
        $namespaceName = $this->currentClass->namespacedName;
        if (!$namespaceName instanceof Name) {
            throw new ShouldNotHappenException();
        }
        return $namespaceName->toString();
    }
    private function doesClassExist(string $className) : bool
    {
        if (\class_exists($className)) {
            return \true;
        }
        if (\interface_exists($className)) {
            return \true;
        }
        return \trait_exists($className);
    }
}

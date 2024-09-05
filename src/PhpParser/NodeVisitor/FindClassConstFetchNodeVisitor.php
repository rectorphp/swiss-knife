<?php

declare(strict_types=1);

namespace Rector\SwissKnife\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use Rector\SwissKnife\Contract\ClassConstantFetchInterface;
use Rector\SwissKnife\Exception\NotImplementedYetException;
use Rector\SwissKnife\Exception\ShouldNotHappenException;
use Rector\SwissKnife\ValueObject\ClassConstantFetch\CurrentClassConstantFetch;
use Rector\SwissKnife\ValueObject\ClassConstantFetch\ExternalClassAccessConstantFetch;
use Rector\SwissKnife\ValueObject\ClassConstantFetch\ParentClassConstantFetch;
use ReflectionClass;
use Webmozart\Assert\Assert;

final class FindClassConstFetchNodeVisitor extends NodeVisitorAbstract
{
    private ?Class_ $currentClass = null;

    /**
     * @var ClassConstantFetchInterface[]
     */
    private array $classConstantFetches = [];

    public function enterNode(Node $node): Node|int|null
    {
        if ($node instanceof Class_) {
            // skip anonymous classes as problematic
            if ($node->isAnonymous()) {
                return NodeTraverser::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
            }

            $this->currentClass = $node;
            return null;
        }

        if (! $node instanceof ClassConstFetch) {
            return null;
        }

        // unable to resolve → skip
        if (! $node->class instanceof Name) {
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
        if ($className === 'self') {
            $currentClassName = $this->getClassName();
            Assert::isInstanceOf($this->currentClass, Class_::class);
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

        if ($className === 'static') {
            throw new NotImplementedYetException('@todo');
        }

        if (class_exists($className) || interface_exists($className)) {
            // is class from /vendor? we can skip it
            if ($this->isVendorClassName($className)) {
                return null;
            }

            // is vendor fetch? skip
            $this->classConstantFetches[] = new ExternalClassAccessConstantFetch($className, $constantName);
            return null;
        }

        throw new NotImplementedYetException();
    }

    public function leaveNode(Node $node): ?Node
    {
        if (! $node instanceof Class_) {
            return null;
        }

        // we've left class, lets reset its value
        $this->currentClass = null;
        return $node;
    }

    /**
     * @return ClassConstantFetchInterface[]
     */
    public function getClassConstantFetches(): array
    {
        return $this->classConstantFetches;
    }

    private function isVendorClassName(string $className): bool
    {
        if (! class_exists($className) && ! interface_exists($className) && ! trait_exists($className)) {
            throw new ShouldNotHappenException();
        }

        $reflectionClass = new ReflectionClass($className);
        return str_contains((string) $reflectionClass->getFileName(), 'vendor');
    }

    private function isCurrentClassConstant(Class_ $currentClass, string $constantName): bool
    {
        foreach ($currentClass->getConstants() as $classConstant) {
            foreach ($classConstant->consts as $const) {
                if ($const->name->toString() === $constantName) {
                    return true;
                }
            }
        }

        return false;
    }

    private function getClassName(): string
    {
        if (! $this->currentClass instanceof Class_) {
            throw new ShouldNotHappenException();
        }

        $namespaceName = $this->currentClass->namespacedName;
        if (! $namespaceName instanceof Name) {
            throw new ShouldNotHappenException();
        }

        return $namespaceName->toString();
    }
}

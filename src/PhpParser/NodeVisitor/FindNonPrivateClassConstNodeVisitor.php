<?php

declare(strict_types=1);

namespace Rector\SwissKnife\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeVisitorAbstract;
use Rector\SwissKnife\ValueObject\ClassConstant;
use ReflectionClass;
use Webmozart\Assert\Assert;

final class FindNonPrivateClassConstNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var ClassConstant[]
     */
    private array $classConstants = [];

    public function enterNode(Node $node): ?Node
    {
        if (! $node instanceof Class_) {
            return null;
        }

        if ($node->isAnonymous() || $node->isAbstract()) {
            return null;
        }

        Assert::isInstanceOf($node->namespacedName, Name::class);

        $className = $node->namespacedName->toString();
        foreach ($node->getConstants() as $classConst) {
            foreach ($classConst->consts as $constConst) {
                $constantName = $constConst->name->toString();

                // not interested in private constants
                if ($classConst->isPrivate()) {
                    continue;
                }

                if ($this->isConstantDefinedInParentClassAlso($node, $constantName)) {
                    continue;
                }

                $this->classConstants[] = new ClassConstant($className, $constantName, $classConst->getLine());
            }
        }

        return $node;
    }

    /**
     * @return ClassConstant[]
     */
    public function getClassConstants(): array
    {
        return $this->classConstants;
    }

    private function isConstantDefinedInParentClassAlso(Class_ $class, string $constantName): bool
    {
        if ($class->extends instanceof Node) {
            $parentClassName = $class->extends->toString();
            if (class_exists($parentClassName)) {
                return in_array($constantName, $this->getClassConstantNames($parentClassName), true);
            }
        }

        foreach ($class->implements as $implement) {
            if (in_array($constantName, $this->getClassConstantNames($implement->toString()), true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string[]
     */
    private function getClassConstantNames(string $className): array
    {
        $reflectionClass = new ReflectionClass($className);

        return array_keys($reflectionClass->getConstants());
    }
}

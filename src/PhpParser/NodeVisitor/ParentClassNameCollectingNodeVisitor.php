<?php

declare (strict_types=1);
namespace Rector\SwissKnife\PhpParser\NodeVisitor;

use SwissKnife202412\PhpParser\Node;
use SwissKnife202412\PhpParser\Node\Name;
use SwissKnife202412\PhpParser\Node\Stmt\Class_;
use SwissKnife202412\PhpParser\NodeVisitorAbstract;
final class ParentClassNameCollectingNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private array $parentClassNames = [];
    public function enterNode(Node $node) : ?Node
    {
        if (!$node instanceof Class_) {
            return null;
        }
        if (!$node->extends instanceof Name) {
            return null;
        }
        $this->parentClassNames[] = $node->extends->toString();
        return $node;
    }
    /**
     * @return string[]
     */
    public function getParentClassNames() : array
    {
        $uniqueParentClassNames = \array_unique($this->parentClassNames);
        \sort($uniqueParentClassNames);
        // remove native classes
        $namespacedClassNames = \array_filter($uniqueParentClassNames, static fn(string $parentClassName): bool => \strpos($parentClassName, '\\') !== \false);
        // remove obviously vendor names
        $namespacedClassNames = \array_filter($namespacedClassNames, static function (string $className) : bool {
            if (\strpos($className, 'Symfony\\') !== \false) {
                return \false;
            }
            if (\strpos($className, 'PHPStan\\') !== \false) {
                return \false;
            }
            return \strpos($className, 'PhpParser\\') === \false;
        });
        return \array_values($namespacedClassNames);
    }
}

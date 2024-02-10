<?php

declare (strict_types=1);
namespace EasyCI202402\Rector\SwissKnife\PhpParser\NodeVisitor;

use EasyCI202402\PhpParser\Node;
use EasyCI202402\PhpParser\Node\Name;
use EasyCI202402\PhpParser\Node\Stmt\Class_;
use EasyCI202402\PhpParser\NodeVisitorAbstract;
final class ParentClassNameCollectingNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private $parentClassNames = [];
    public function enterNode(Node $node)
    {
        if (!$node instanceof Class_) {
            return null;
        }
        if (!$node->extends instanceof Name) {
            return null;
        }
        $this->parentClassNames[] = $node->extends->toString();
        return null;
    }
    /**
     * @return string[]
     */
    public function getParentClassNames() : array
    {
        $uniqueParentClassNames = \array_unique($this->parentClassNames);
        \sort($uniqueParentClassNames);
        // remove native classes
        $namespacedClassNames = \array_filter($uniqueParentClassNames, static function (string $parentClassName) : bool {
            return \strpos($parentClassName, '\\') !== \false;
        });
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

<?php

declare (strict_types=1);
namespace Rector\SwissKnife\PhpParser\NodeVisitor;

use SwissKnife202412\PhpParser\Node;
use SwissKnife202412\PhpParser\Node\Name;
use SwissKnife202412\PhpParser\Node\Stmt\Class_;
use SwissKnife202412\PhpParser\NodeVisitorAbstract;
final class NeedForFinalizeNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     * @readonly
     */
    private array $excludedClasses;
    private bool $isNeeded = \false;
    /**
     * @param string[] $excludedClasses
     */
    public function __construct(array $excludedClasses)
    {
        $this->excludedClasses = $excludedClasses;
    }
    /**
     * @param Node[] $nodes
     * @return Node[]
     */
    public function beforeTraverse(array $nodes) : array
    {
        // reset
        $this->isNeeded = \false;
        return $nodes;
    }
    public function enterNode(Node $node) : ?Node
    {
        if (!$node instanceof Class_) {
            return null;
        }
        // nothing we can do
        if ($node->isFinal() || $node->isAnonymous() || $node->isAbstract()) {
            return null;
        }
        // we need a name to make it work
        if (!$node->namespacedName instanceof Name) {
            return null;
        }
        $className = $node->namespacedName->toString();
        if (\in_array($className, $this->excludedClasses, \true)) {
            return null;
        }
        $this->isNeeded = \true;
        return $node;
    }
    public function isNeeded() : bool
    {
        return $this->isNeeded;
    }
}

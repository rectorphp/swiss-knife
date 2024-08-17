<?php

declare(strict_types=1);

namespace Rector\SwissKnife\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeVisitorAbstract;

final class NeedForFinalizeNodeVisitor extends NodeVisitorAbstract
{
    private bool $isNeeded = false;

    /**
     * @param string[] $excludedClasses
     */
    public function __construct(
        private readonly array $excludedClasses
    ) {
    }

    /**
     * @param Node[] $nodes
     * @return Node[]
     */
    public function beforeTraverse(array $nodes): array
    {
        // reset
        $this->isNeeded = false;

        return $nodes;
    }

    public function enterNode(Node $node): ?Node
    {
        if (! $node instanceof Class_) {
            return null;
        }

        // nothing we can do
        if ($node->isFinal() || $node->isAnonymous() || $node->isAbstract()) {
            return null;
        }

        // we need a name to make it work
        if (! $node->namespacedName instanceof Name) {
            return null;
        }

        $className = $node->namespacedName->toString();
        if (in_array($className, $this->excludedClasses, true)) {
            return null;
        }

        $this->isNeeded = true;

        return $node;
    }

    public function isNeeded(): bool
    {
        return $this->isNeeded;
    }
}

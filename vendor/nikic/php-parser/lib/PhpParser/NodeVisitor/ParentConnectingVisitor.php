<?php

declare (strict_types=1);
namespace SwissKnife202502\PhpParser\NodeVisitor;

use SwissKnife202502\PhpParser\Node;
use SwissKnife202502\PhpParser\NodeVisitorAbstract;
use function array_pop;
use function count;
/**
 * Visitor that connects a child node to its parent node.
 *
 * On the child node, the parent node can be accessed through
 * <code>$node->getAttribute('parent')</code>.
 */
final class ParentConnectingVisitor extends NodeVisitorAbstract
{
    /**
     * @var Node[]
     */
    private $stack = [];
    public function beforeTraverse(array $nodes)
    {
        $this->stack = [];
    }
    public function enterNode(Node $node)
    {
        if (!empty($this->stack)) {
            $node->setAttribute('parent', $this->stack[count($this->stack) - 1]);
        }
        $this->stack[] = $node;
    }
    public function leaveNode(Node $node)
    {
        array_pop($this->stack);
    }
}

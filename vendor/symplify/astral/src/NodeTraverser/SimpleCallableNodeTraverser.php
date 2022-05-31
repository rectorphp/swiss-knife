<?php

declare (strict_types=1);
namespace EasyCI20220531\Symplify\Astral\NodeTraverser;

use EasyCI20220531\PhpParser\Node;
use EasyCI20220531\PhpParser\NodeTraverser;
use EasyCI20220531\Symplify\Astral\NodeVisitor\CallableNodeVisitor;
/**
 * @api
 */
final class SimpleCallableNodeTraverser
{
    /**
     * @param callable(Node $node): (int|Node|null) $callable
     * @param \PhpParser\Node|mixed[]|null $nodes
     */
    public function traverseNodesWithCallable($nodes, callable $callable) : void
    {
        if ($nodes === null) {
            return;
        }
        if ($nodes === []) {
            return;
        }
        if (!\is_array($nodes)) {
            $nodes = [$nodes];
        }
        $nodeTraverser = new \EasyCI20220531\PhpParser\NodeTraverser();
        $callableNodeVisitor = new \EasyCI20220531\Symplify\Astral\NodeVisitor\CallableNodeVisitor($callable);
        $nodeTraverser->addVisitor($callableNodeVisitor);
        $nodeTraverser->traverse($nodes);
    }
}

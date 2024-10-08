<?php

declare(strict_types=1);

namespace Rector\SwissKnife\PhpParser;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;

/**
 * To avoid dynamic count of node visitors in single node traverser
 */
final class NodeTraverserFactory
{
    public static function create(NodeVisitor $nodeVisitor): NodeTraverser
    {
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($nodeVisitor);

        return $nodeTraverser;
    }
}

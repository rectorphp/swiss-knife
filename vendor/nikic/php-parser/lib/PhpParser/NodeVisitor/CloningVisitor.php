<?php

declare (strict_types=1);
namespace EasyCI20220205\PhpParser\NodeVisitor;

use EasyCI20220205\PhpParser\Node;
use EasyCI20220205\PhpParser\NodeVisitorAbstract;
/**
 * Visitor cloning all nodes and linking to the original nodes using an attribute.
 *
 * This visitor is required to perform format-preserving pretty prints.
 */
class CloningVisitor extends \EasyCI20220205\PhpParser\NodeVisitorAbstract
{
    public function enterNode(\EasyCI20220205\PhpParser\Node $origNode)
    {
        $node = clone $origNode;
        $node->setAttribute('origNode', $origNode);
        return $node;
    }
}

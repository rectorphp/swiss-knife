<?php

declare (strict_types=1);
namespace EasyCI20220224\PhpParser\NodeVisitor;

use EasyCI20220224\PhpParser\Node;
use EasyCI20220224\PhpParser\NodeVisitorAbstract;
/**
 * Visitor cloning all nodes and linking to the original nodes using an attribute.
 *
 * This visitor is required to perform format-preserving pretty prints.
 */
class CloningVisitor extends \EasyCI20220224\PhpParser\NodeVisitorAbstract
{
    public function enterNode(\EasyCI20220224\PhpParser\Node $origNode)
    {
        $node = clone $origNode;
        $node->setAttribute('origNode', $origNode);
        return $node;
    }
}

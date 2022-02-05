<?php

declare (strict_types=1);
namespace EasyCI20220205\PhpParser;

/**
 * @codeCoverageIgnore
 */
class NodeVisitorAbstract implements \EasyCI20220205\PhpParser\NodeVisitor
{
    public function beforeTraverse(array $nodes)
    {
        return null;
    }
    public function enterNode(\EasyCI20220205\PhpParser\Node $node)
    {
        return null;
    }
    public function leaveNode(\EasyCI20220205\PhpParser\Node $node)
    {
        return null;
    }
    public function afterTraverse(array $nodes)
    {
        return null;
    }
}

<?php

declare (strict_types=1);
namespace Symplify\EasyCI\ActiveClass\NodeVisitor;

use EasyCI20220215\PhpParser\Node;
use EasyCI20220215\PhpParser\Node\Name;
use EasyCI20220215\PhpParser\Node\Stmt;
use EasyCI20220215\PhpParser\Node\Stmt\ClassMethod;
use EasyCI20220215\PhpParser\Node\Stmt\Namespace_;
use EasyCI20220215\PhpParser\NodeVisitorAbstract;
use EasyCI20220215\Symplify\Astral\ValueObject\AttributeKey;
final class UsedClassNodeVisitor extends \EasyCI20220215\PhpParser\NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private $usedNames = [];
    /**
     * @param Stmt[] $nodes
     * @return Stmt[]
     */
    public function beforeTraverse(array $nodes) : ?array
    {
        $this->usedNames = [];
        return $nodes;
    }
    public function enterNode(\EasyCI20220215\PhpParser\Node $node)
    {
        if (!$node instanceof \EasyCI20220215\PhpParser\Node\Name) {
            return null;
        }
        if ($this->isNonNameNode($node)) {
            return null;
        }
        // class names itself are skipped automatically, as they are Identifier node
        $this->usedNames[] = $node->toString();
        return null;
    }
    /**
     * @return string[]
     */
    public function getUsedNames() : array
    {
        $uniqueUsedNames = \array_unique($this->usedNames);
        \sort($uniqueUsedNames);
        return $uniqueUsedNames;
    }
    private function isNonNameNode(\EasyCI20220215\PhpParser\Node\Name $name) : bool
    {
        // skip nodes that are not part of class names
        $parent = $name->getAttribute(\EasyCI20220215\Symplify\Astral\ValueObject\AttributeKey::PARENT);
        if ($parent instanceof \EasyCI20220215\PhpParser\Node\Stmt\Namespace_) {
            return \true;
        }
        return $parent instanceof \EasyCI20220215\PhpParser\Node\Stmt\ClassMethod;
    }
}

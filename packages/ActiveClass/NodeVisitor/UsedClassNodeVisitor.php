<?php

declare (strict_types=1);
namespace Symplify\EasyCI\ActiveClass\NodeVisitor;

use EasyCI20220610\PhpParser\Node;
use EasyCI20220610\PhpParser\Node\Name;
use EasyCI20220610\PhpParser\Node\Stmt;
use EasyCI20220610\PhpParser\Node\Stmt\ClassMethod;
use EasyCI20220610\PhpParser\Node\Stmt\Namespace_;
use EasyCI20220610\PhpParser\NodeVisitorAbstract;
use EasyCI20220610\Symplify\Astral\ValueObject\AttributeKey;
final class UsedClassNodeVisitor extends NodeVisitorAbstract
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
    public function enterNode(Node $node)
    {
        if (!$node instanceof Name) {
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
    private function isNonNameNode(Name $name) : bool
    {
        // skip nodes that are not part of class names
        $parent = $name->getAttribute(AttributeKey::PARENT);
        if ($parent instanceof Namespace_) {
            return \true;
        }
        return $parent instanceof ClassMethod;
    }
}

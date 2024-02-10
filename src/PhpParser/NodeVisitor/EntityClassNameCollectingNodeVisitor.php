<?php

declare (strict_types=1);
namespace EasyCI202402\Rector\SwissKnife\PhpParser\NodeVisitor;

use EasyCI202402\PhpParser\Comment\Doc;
use EasyCI202402\PhpParser\Node;
use EasyCI202402\PhpParser\Node\Name;
use EasyCI202402\PhpParser\Node\Stmt\Class_;
use EasyCI202402\PhpParser\NodeVisitorAbstract;
final class EntityClassNameCollectingNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private $entityClassNames = [];
    public function enterNode(Node $node)
    {
        if (!$node instanceof Class_) {
            return null;
        }
        // must be named
        if (!$node->namespacedName instanceof Name) {
            return null;
        }
        if ($this->hasEntityDocBlock($node) || $this->hasEntityAttribute($node)) {
            $this->entityClassNames[] = $node->namespacedName->toString();
            return null;
        }
        return null;
    }
    /**
     * @return string[]
     */
    public function getEntityClassNames() : array
    {
        $uniqueEntityClassNames = \array_unique($this->entityClassNames);
        \sort($uniqueEntityClassNames);
        return $uniqueEntityClassNames;
    }
    private function hasEntityDocBlock(Class_ $class) : bool
    {
        $docComment = $class->getDocComment();
        if ($docComment instanceof Doc) {
            // dummy check
            if (\strpos($docComment->getText(), '@') === \false) {
                return \false;
            }
            if (\strpos($docComment->getText(), 'Entity') !== \false) {
                return \true;
            }
            if (\strpos($docComment->getText(), 'Embeddable') !== \false) {
                return \true;
            }
        }
        return \false;
    }
    private function hasEntityAttribute(Class_ $class) : bool
    {
        foreach ($class->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                if (\substr_compare($attr->name->toString(), 'Entity', -\strlen('Entity')) === 0) {
                    return \true;
                }
                if (\substr_compare($attr->name->toString(), 'Embeddable', -\strlen('Embeddable')) === 0) {
                    return \true;
                }
            }
        }
        return \false;
    }
}
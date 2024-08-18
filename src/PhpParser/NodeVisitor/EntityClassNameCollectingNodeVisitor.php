<?php

declare(strict_types=1);

namespace Rector\SwissKnife\PhpParser\NodeVisitor;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeVisitorAbstract;
use Webmozart\Assert\Assert;

final class EntityClassNameCollectingNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private const ODM_SUFFIXES = ['Document', 'EmbeddedDocument'];

    /**
     * @var string[]
     */
    private const ORM_SUFFIXES = ['Entity', 'Embeddable'];

    /**
     * @var string[]
     */
    private array $entityClassNames = [];

    public function enterNode(Node $node): ?Node
    {
        if (! $node instanceof Class_) {
            return null;
        }

        // must be named
        if (! $node->namespacedName instanceof Name) {
            return null;
        }

        if ($this->hasEntityAnnotation($node) || $this->hasEntityAttribute($node)) {
            $this->entityClassNames[] = $node->namespacedName->toString();
            return null;
        }

        return $node;
    }

    /**
     * @return string[]
     */
    public function getEntityClassNames(): array
    {
        $uniqueEntityClassNames = array_unique($this->entityClassNames);
        sort($uniqueEntityClassNames);

        return $uniqueEntityClassNames;
    }

    /**
     * @param string[] $suffixes
     */
    private function hasDocBlockSuffixes(Class_ $class, array $suffixes): bool
    {
        Assert::allString($suffixes);

        $docComment = $class->getDocComment();
        if ($docComment instanceof Doc) {
            // dummy check
            if (! str_contains($docComment->getText(), '@')) {
                return false;
            }

            foreach ($suffixes as $suffix) {
                if (str_contains($docComment->getText(), $suffix)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string[] $suffixes
     */
    private function hasAttributeSuffixes(Class_ $class, array $suffixes): bool
    {
        Assert::allString($suffixes);

        foreach ($class->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                foreach ($suffixes as $suffix) {
                    if (str_ends_with($attr->name->toString(), $suffix)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    private function hasEntityAnnotation(Class_ $class): bool
    {
        if ($this->hasDocBlockSuffixes($class, self::ODM_SUFFIXES)) {
            return true;
        }

        return $this->hasDocBlockSuffixes($class, self::ORM_SUFFIXES);
    }

    private function hasEntityAttribute(Class_ $class): bool
    {
        if ($this->hasAttributeSuffixes($class, self::ODM_SUFFIXES)) {
            return true;
        }

        return $this->hasAttributeSuffixes($class, self::ORM_SUFFIXES);
    }
}

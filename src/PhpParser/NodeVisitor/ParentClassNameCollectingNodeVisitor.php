<?php

declare(strict_types=1);

namespace Rector\SwissKnife\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeVisitorAbstract;

final class ParentClassNameCollectingNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private array $parentClassNames = [];

    public function enterNode(Node $node): ?Node
    {
        if (! $node instanceof Class_) {
            return null;
        }

        if (! $node->extends instanceof Name) {
            return null;
        }

        $this->parentClassNames[] = $node->extends->toString();

        return $node;
    }

    /**
     * @return string[]
     */
    public function getParentClassNames(): array
    {
        $uniqueParentClassNames = array_unique($this->parentClassNames);
        sort($uniqueParentClassNames);

        // remove native classes
        $namespacedClassNames = array_filter(
            $uniqueParentClassNames,
            static fn (string $parentClassName): bool => str_contains($parentClassName, '\\')
        );

        // remove obviously vendor names
        $namespacedClassNames = array_filter($namespacedClassNames, static function (string $className): bool {
            if (str_contains($className, 'Symfony\\')) {
                return false;
            }

            if (str_contains($className, 'PHPStan\\')) {
                return false;
            }

            return ! str_contains($className, 'PhpParser\\');
        });

        return array_values($namespacedClassNames);
    }
}

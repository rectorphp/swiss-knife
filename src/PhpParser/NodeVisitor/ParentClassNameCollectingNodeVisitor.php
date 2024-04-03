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

    public function enterNode(Node $node)
    {
        if (! $node instanceof Class_) {
            return null;
        }

        if (! $node->extends instanceof Name) {
            return null;
        }

        $this->parentClassNames[] = $node->extends->toString();

        return null;
    }

    /**
     * @return string[]
     */
    public function getParentClassNames(): array
    {
        $uniqueParentClassNames = array_unique($this->parentClassNames);
        sort($uniqueParentClassNames);

        $namespacedClassNames = [];
        foreach($uniqueParentClassNames as $className) {
            try {
                // @phpstan-ignore-next-line
                $reflectionClass = new \ReflectionClass($className);
                if ($reflectionClass->isInternal()) {
                    // remove native classes
                    continue;
                }
                $namespacedClassNames[] = $className;
            } catch (\ReflectionException $e) {
                $namespacedClassNames[] = $className;
            }
        }

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

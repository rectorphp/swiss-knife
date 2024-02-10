<?php

declare(strict_types=1);

namespace Rector\SwissKnife;

use PhpParser\NodeTraverser;
use Rector\SwissKnife\PhpParser\CachedPhpParser;
use Rector\SwissKnife\PhpParser\NodeVisitor\EntityClassNameCollectingNodeVisitor;
use Symfony\Component\Finder\SplFileInfo;

final class EntityClassResolver
{
    public function __construct(
        private CachedPhpParser $cachedPhpParser
    ) {
    }

    /**
     * @param SplFileInfo[] $phpFileInfos
     * @return string[]
     */
    public function resolve(array $phpFileInfos, \Closure $progressClosure): array
    {
        $entityClassNameCollectingNodeVisitor = new EntityClassNameCollectingNodeVisitor();

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($entityClassNameCollectingNodeVisitor);

        $this->traverseFileInfos($phpFileInfos, $nodeTraverser, $progressClosure);

        return $entityClassNameCollectingNodeVisitor->getEntityClassNames();
    }

    /**
     * @param SplFileInfo[] $phpFileInfos
     */
    private function traverseFileInfos(
        array $phpFileInfos,
        NodeTraverser $nodeTraverser,
        callable $progressClosure
    ): void {
        foreach ($phpFileInfos as $phpFileInfo) {
            $stmts = $this->cachedPhpParser->parseFile($phpFileInfo->getRealPath());

            $nodeTraverser->traverse($stmts);
            $progressClosure();
        }
    }
}

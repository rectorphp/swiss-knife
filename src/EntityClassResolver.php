<?php

declare(strict_types=1);

namespace TomasVotruba\Finalize;

use PhpParser\NodeTraverser;
use Symfony\Component\Finder\SplFileInfo;
use TomasVotruba\Finalize\NodeVisitor\EntityClassNameCollectingNodeVisitor;
use TomasVotruba\Finalize\PhpParser\CachedPhpParser;

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

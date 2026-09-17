<?php

declare(strict_types=1);

namespace Rector\SwissKnife;

use PhpParser\NodeTraverser;
use Rector\SwissKnife\PhpParser\CachedPhpParser;
use Rector\SwissKnife\PhpParser\NodeTraverserFactory;
use Rector\SwissKnife\PhpParser\NodeVisitor\ParentClassNameCollectingNodeVisitor;
use Rector\SwissKnife\ValueObject\FileInfo;

final readonly class ParentClassResolver
{
    public function __construct(
        private CachedPhpParser $cachedPhpParser,
    ) {
    }

    /**
     * @param FileInfo[] $phpFileInfos
     * @return string[]
     */
    public function resolve(array $phpFileInfos, callable $progressClosure): array
    {
        $parentClassNameCollectingNodeVisitor = new ParentClassNameCollectingNodeVisitor();
        $nodeTraverser = NodeTraverserFactory::create($parentClassNameCollectingNodeVisitor);

        $this->traverseFileInfos($phpFileInfos, $nodeTraverser, $progressClosure);

        return $parentClassNameCollectingNodeVisitor->getParentClassNames();
    }

    /**
     * @param FileInfo[] $phpFileInfos
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

<?php

declare(strict_types=1);

namespace TomasVotruba\Lemonade;

use PhpParser\NodeTraverser;
use Symfony\Component\Finder\SplFileInfo;
use TomasVotruba\Lemonade\PhpParser\CachedPhpParser;
use TomasVotruba\Lemonade\PhpParser\NodeVisitor\ParentClassNameCollectingNodeVisitor;

final readonly class ParentClassResolver
{
    public function __construct(
        private CachedPhpParser $cachedPhpParser
    ) {
    }

    /**
     * @param SplFileInfo[] $phpFileInfos
     * @return string[]
     */
    public function resolve(array $phpFileInfos, callable $progressClosure): array
    {
        $parentClassNameCollectingNodeVisitor = new ParentClassNameCollectingNodeVisitor();

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($parentClassNameCollectingNodeVisitor);

        $this->traverseFileInfos($phpFileInfos, $nodeTraverser, $progressClosure);

        return $parentClassNameCollectingNodeVisitor->getParentClassNames();
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

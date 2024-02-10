<?php

declare(strict_types=1);

<<<<<<< HEAD
namespace Rector\SwissKnife;

use PhpParser\NodeTraverser;
use Rector\SwissKnife\PhpParser\CachedPhpParser;
use Rector\SwissKnife\PhpParser\NodeVisitor\ParentClassNameCollectingNodeVisitor;
use Symfony\Component\Finder\SplFileInfo;

final readonly class ParentClassResolver
=======
namespace TomasVotruba\Finalize;

use PhpParser\NodeTraverser;
use Symfony\Component\Finder\SplFileInfo;
use TomasVotruba\Finalize\NodeVisitor\ParentClassNameCollectingNodeVisitor;
use TomasVotruba\Finalize\PhpParser\CachedPhpParser;

final class ParentClassResolver
>>>>>>> d1cceb0f6 (misc)
{
    public function __construct(
        private CachedPhpParser $cachedPhpParser
    ) {
    }

    /**
     * @param SplFileInfo[] $phpFileInfos
     * @return string[]
     */
<<<<<<< HEAD
    public function resolve(array $phpFileInfos, callable $progressClosure): array
=======
    public function resolve(array $phpFileInfos, \Closure $progressClosure): array
>>>>>>> d1cceb0f6 (misc)
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

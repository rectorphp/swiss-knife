<?php

declare(strict_types=1);

<<<<<<< HEAD
namespace Rector\SwissKnife;

use Closure;
use PhpParser\NodeTraverser;
use Rector\SwissKnife\PhpParser\CachedPhpParser;
use Rector\SwissKnife\PhpParser\NodeVisitor\EntityClassNameCollectingNodeVisitor;
use Symfony\Component\Finder\SplFileInfo;

final readonly class EntityClassResolver
=======
namespace TomasVotruba\Finalize;

use PhpParser\NodeTraverser;
use Symfony\Component\Finder\SplFileInfo;
use TomasVotruba\Finalize\NodeVisitor\EntityClassNameCollectingNodeVisitor;
use TomasVotruba\Finalize\PhpParser\CachedPhpParser;

final class EntityClassResolver
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
    public function resolve(array $phpFileInfos, Closure $progressClosure): array
=======
    public function resolve(array $phpFileInfos, \Closure $progressClosure): array
>>>>>>> d1cceb0f6 (misc)
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

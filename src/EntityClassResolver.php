<?php

declare (strict_types=1);
namespace EasyCI202402\Rector\SwissKnife;

use Closure;
use EasyCI202402\PhpParser\NodeTraverser;
use EasyCI202402\Rector\SwissKnife\PhpParser\CachedPhpParser;
use EasyCI202402\Rector\SwissKnife\PhpParser\NodeVisitor\EntityClassNameCollectingNodeVisitor;
use EasyCI202402\Symfony\Component\Finder\SplFileInfo;
final class EntityClassResolver
{
    /**
     * @readonly
     * @var \Rector\SwissKnife\PhpParser\CachedPhpParser
     */
    private $cachedPhpParser;
    public function __construct(CachedPhpParser $cachedPhpParser)
    {
        $this->cachedPhpParser = $cachedPhpParser;
    }
    /**
     * @param SplFileInfo[] $phpFileInfos
     * @return string[]
     */
    public function resolve(array $phpFileInfos, Closure $progressClosure) : array
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
    private function traverseFileInfos(array $phpFileInfos, NodeTraverser $nodeTraverser, callable $progressClosure) : void
    {
        foreach ($phpFileInfos as $phpFileInfo) {
            $stmts = $this->cachedPhpParser->parseFile($phpFileInfo->getRealPath());
            $nodeTraverser->traverse($stmts);
            $progressClosure();
        }
    }
}

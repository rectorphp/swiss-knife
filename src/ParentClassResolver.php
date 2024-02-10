<?php

declare (strict_types=1);
namespace EasyCI202402\Rector\SwissKnife;

use EasyCI202402\PhpParser\NodeTraverser;
use EasyCI202402\Rector\SwissKnife\PhpParser\CachedPhpParser;
use EasyCI202402\Rector\SwissKnife\PhpParser\NodeVisitor\ParentClassNameCollectingNodeVisitor;
use EasyCI202402\Symfony\Component\Finder\SplFileInfo;
final class ParentClassResolver
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
    public function resolve(array $phpFileInfos, callable $progressClosure) : array
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
    private function traverseFileInfos(array $phpFileInfos, NodeTraverser $nodeTraverser, callable $progressClosure) : void
    {
        foreach ($phpFileInfos as $phpFileInfo) {
            $stmts = $this->cachedPhpParser->parseFile($phpFileInfo->getRealPath());
            $nodeTraverser->traverse($stmts);
            $progressClosure();
        }
    }
}

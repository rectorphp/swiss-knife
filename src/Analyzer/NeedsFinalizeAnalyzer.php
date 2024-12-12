<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Analyzer;

use SwissKnife202412\PhpParser\NodeTraverser;
use Rector\SwissKnife\PhpParser\CachedPhpParser;
use Rector\SwissKnife\PhpParser\NodeTraverserFactory;
use Rector\SwissKnife\PhpParser\NodeVisitor\NeedForFinalizeNodeVisitor;
use SwissKnife202412\Webmozart\Assert\Assert;
final class NeedsFinalizeAnalyzer
{
    /**
     * @readonly
     */
    private CachedPhpParser $cachedPhpParser;
    /**
     * @readonly
     */
    private NodeTraverser $finalizingNodeTraverser;
    /**
     * @readonly
     */
    private NeedForFinalizeNodeVisitor $needForFinalizeNodeVisitor;
    /**
     * @param string[] $excludedClasses
     */
    public function __construct(array $excludedClasses, CachedPhpParser $cachedPhpParser)
    {
        $this->cachedPhpParser = $cachedPhpParser;
        Assert::allString($excludedClasses);
        $this->needForFinalizeNodeVisitor = new NeedForFinalizeNodeVisitor($excludedClasses);
        $finalizingNodeTraverser = NodeTraverserFactory::create($this->needForFinalizeNodeVisitor);
        $this->finalizingNodeTraverser = $finalizingNodeTraverser;
    }
    public function isNeeded(string $filePath) : bool
    {
        $stmts = $this->cachedPhpParser->parseFile($filePath);
        $this->finalizingNodeTraverser->traverse($stmts);
        return $this->needForFinalizeNodeVisitor->isNeeded();
    }
}

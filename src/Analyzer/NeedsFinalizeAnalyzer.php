<?php

declare (strict_types=1);
namespace EasyCI202402\Rector\SwissKnife\Analyzer;

use EasyCI202402\PhpParser\NodeTraverser;
use EasyCI202402\Rector\SwissKnife\PhpParser\CachedPhpParser;
use EasyCI202402\Rector\SwissKnife\PhpParser\NodeVisitor\NeedForFinalizeNodeVisitor;
use EasyCI202402\Webmozart\Assert\Assert;
final class NeedsFinalizeAnalyzer
{
    /**
     * @readonly
     * @var \Rector\SwissKnife\PhpParser\CachedPhpParser
     */
    private $cachedPhpParser;
    /**
     * @readonly
     * @var \PhpParser\NodeTraverser
     */
    private $finalizingNodeTraverser;
    /**
     * @readonly
     * @var \Rector\SwissKnife\PhpParser\NodeVisitor\NeedForFinalizeNodeVisitor
     */
    private $needForFinalizeNodeVisitor;
    /**
     * @param string[] $excludedClasses
     */
    public function __construct(array $excludedClasses, CachedPhpParser $cachedPhpParser)
    {
        $this->cachedPhpParser = $cachedPhpParser;
        Assert::allString($excludedClasses);
        $finalizingNodeTraverser = new NodeTraverser();
        $this->needForFinalizeNodeVisitor = new NeedForFinalizeNodeVisitor($excludedClasses);
        $finalizingNodeTraverser->addVisitor($this->needForFinalizeNodeVisitor);
        $this->finalizingNodeTraverser = $finalizingNodeTraverser;
    }
    public function isNeeded(string $filePath) : bool
    {
        $stmts = $this->cachedPhpParser->parseFile($filePath);
        $this->finalizingNodeTraverser->traverse($stmts);
        return $this->needForFinalizeNodeVisitor->isNeeded();
    }
}
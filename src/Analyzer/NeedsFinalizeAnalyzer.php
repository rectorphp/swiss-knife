<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Analyzer;

use PhpParser\NodeTraverser;
use Rector\SwissKnife\PhpParser\CachedPhpParser;
use Rector\SwissKnife\PhpParser\NodeVisitor\NeedForFinalizeNodeVisitor;
use Webmozart\Assert\Assert;

final readonly class NeedsFinalizeAnalyzer
{
    private NodeTraverser $finalizingNodeTraverser;

    private NeedForFinalizeNodeVisitor $needForFinalizeNodeVisitor;

    /**
     * @param string[] $excludedClasses
     */
    public function __construct(
        array $excludedClasses,
        private CachedPhpParser $cachedPhpParser
    ) {
        Assert::allString($excludedClasses);

        $finalizingNodeTraverser = new NodeTraverser();
        $this->needForFinalizeNodeVisitor = new NeedForFinalizeNodeVisitor($excludedClasses);
        $finalizingNodeTraverser->addVisitor($this->needForFinalizeNodeVisitor);

        $this->finalizingNodeTraverser = $finalizingNodeTraverser;
    }

    public function isNeeded(string $filePath): bool
    {
        $stmts = $this->cachedPhpParser->parseFile($filePath);
        $this->finalizingNodeTraverser->traverse($stmts);

        return $this->needForFinalizeNodeVisitor->isNeeded();
    }
}

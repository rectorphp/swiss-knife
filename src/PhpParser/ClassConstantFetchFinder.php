<?php

declare (strict_types=1);
namespace Rector\SwissKnife\PhpParser;

use SwissKnife202409\PhpParser\NodeTraverser;
use Rector\SwissKnife\Contract\ClassConstantFetchInterface;
use Rector\SwissKnife\PhpParser\NodeVisitor\FindClassConstFetchNodeVisitor;
use SwissKnife202409\Symfony\Component\Console\Helper\ProgressBar;
use SwissKnife202409\Symfony\Component\Finder\SplFileInfo;
/**
 * @see \PhpParser\ClassConstantFetchFinder\ClassConstantFetchFinderTest
 */
final class ClassConstantFetchFinder
{
    /**
     * @var \Rector\SwissKnife\PhpParser\CachedPhpParser
     */
    private $cachedPhpParser;
    public function __construct(\Rector\SwissKnife\PhpParser\CachedPhpParser $cachedPhpParser)
    {
        $this->cachedPhpParser = $cachedPhpParser;
    }
    /**
     * @param SplFileInfo[] $phpFileInfos
     * @return ClassConstantFetchInterface[]
     */
    public function find(array $phpFileInfos, ProgressBar $progressBar) : array
    {
        $nodeTraverser = new NodeTraverser();
        $findClassConstFetchNodeVisitor = new FindClassConstFetchNodeVisitor();
        $nodeTraverser->addVisitor($findClassConstFetchNodeVisitor);
        foreach ($phpFileInfos as $phpFileInfo) {
            $fileStmts = $this->cachedPhpParser->parseFile($phpFileInfo->getRealPath());
            $nodeTraverser->traverse($fileStmts);
            $progressBar->advance();
        }
        $progressBar->finish();
        return $findClassConstFetchNodeVisitor->getClassConstantFetches();
    }
}

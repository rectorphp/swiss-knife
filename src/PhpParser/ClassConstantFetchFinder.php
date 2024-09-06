<?php

declare(strict_types=1);

namespace Rector\SwissKnife\PhpParser;

use PhpParser\NodeTraverser;
use Rector\SwissKnife\Contract\ClassConstantFetchInterface;
use Rector\SwissKnife\PhpParser\NodeVisitor\FindClassConstFetchNodeVisitor;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @see \PhpParser\ClassConstantFetchFinder\ClassConstantFetchFinderTest
 */
final class ClassConstantFetchFinder
{
    public function __construct(
        private CachedPhpParser $cachedPhpParser
    ) {
    }

    /**
     * @param SplFileInfo[] $phpFileInfos
     * @return ClassConstantFetchInterface[]
     */
    public function find(array $phpFileInfos, ProgressBar $progressBar): array
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

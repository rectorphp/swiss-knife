<?php

declare(strict_types=1);

namespace Rector\SwissKnife\PhpParser\Finder;

use Rector\SwissKnife\PhpParser\CachedPhpParser;
use Rector\SwissKnife\PhpParser\NodeTraverserFactory;
use Rector\SwissKnife\PhpParser\NodeVisitor\FindNonPrivateClassConstNodeVisitor;
use Rector\SwissKnife\ValueObject\ClassConstant;

/**
 * @see \Rector\SwissKnife\Tests\PhpParser\Finder\ClassConstFinder\ClassConstFinderTest
 */
final readonly class ClassConstFinder
{
    public function __construct(
        private CachedPhpParser $cachedPhpParser
    ) {
    }

    /**
     * @return ClassConstant[]
     */
    public function find(string $filePath): array
    {
        $findNonPrivateClassConstNodeVisitor = new FindNonPrivateClassConstNodeVisitor();
        $nodeTraverser = NodeTraverserFactory::create($findNonPrivateClassConstNodeVisitor);

        $fileStmts = $this->cachedPhpParser->parseFile($filePath);
        $nodeTraverser->traverse($fileStmts);

        return $findNonPrivateClassConstNodeVisitor->getClassConstants();
    }
}

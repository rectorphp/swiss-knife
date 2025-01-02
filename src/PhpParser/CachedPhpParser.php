<?php

declare (strict_types=1);
namespace Rector\SwissKnife\PhpParser;

use SwissKnife202501\Nette\Utils\FileSystem;
use SwissKnife202501\PhpParser\Node\Stmt;
use SwissKnife202501\PhpParser\NodeVisitor\NameResolver;
use SwissKnife202501\PhpParser\Parser;
/**
 * Parse file just once
 */
final class CachedPhpParser
{
    /**
     * @readonly
     * @var \PhpParser\Parser
     */
    private $phpParser;
    /**
     * @var array<string, Stmt[]>
     */
    private $cachedStmts = [];
    public function __construct(Parser $phpParser)
    {
        $this->phpParser = $phpParser;
    }
    /**
     * @return Stmt[]
     */
    public function parseFile(string $filePath) : array
    {
        if (isset($this->cachedStmts[$filePath])) {
            return $this->cachedStmts[$filePath];
        }
        $fileContents = FileSystem::read($filePath);
        try {
            $stmts = $this->phpParser->parse($fileContents);
        } catch (\Throwable $throwable) {
            throw new \RuntimeException(\sprintf('Could not parse file "%s": %s', $filePath, $throwable->getMessage()), $throwable->getCode(), $throwable);
        }
        if (\is_array($stmts)) {
            $nodeTraverser = \Rector\SwissKnife\PhpParser\NodeTraverserFactory::create(new NameResolver());
            $nodeTraverser->traverse($stmts);
        }
        $this->cachedStmts[$filePath] = $stmts ?? [];
        return $stmts ?? [];
    }
}

<?php

declare (strict_types=1);
namespace Rector\SwissKnife\PhpParser;

use SwissKnife202407\Nette\Utils\FileSystem;
use SwissKnife202407\PhpParser\Node\Stmt;
use SwissKnife202407\PhpParser\NodeTraverser;
use SwissKnife202407\PhpParser\NodeVisitor\NameResolver;
use SwissKnife202407\PhpParser\Parser;
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
        $stmts = $this->phpParser->parse($fileContents);
        if (\is_array($stmts)) {
            $nodeTraverser = new NodeTraverser();
            $nodeTraverser->addVisitor(new NameResolver());
            $nodeTraverser->traverse($stmts);
        }
        $this->cachedStmts[$filePath] = $stmts ?? [];
        return $stmts ?? [];
    }
}

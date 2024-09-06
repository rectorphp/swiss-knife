<?php

declare (strict_types=1);
namespace Rector\SwissKnife\PhpParser;

use SwissKnife202409\Nette\Utils\FileSystem;
use SwissKnife202409\PhpParser\Node\Stmt;
use SwissKnife202409\PhpParser\NodeTraverser;
use SwissKnife202409\PhpParser\NodeVisitor\NameResolver;
use SwissKnife202409\PhpParser\Parser;
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

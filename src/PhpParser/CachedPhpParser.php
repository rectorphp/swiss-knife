<?php

declare (strict_types=1);
namespace EasyCI202402\Rector\SwissKnife\PhpParser;

use EasyCI202402\Nette\Utils\FileSystem;
use EasyCI202402\PhpParser\Node\Stmt;
use EasyCI202402\PhpParser\NodeTraverser;
use EasyCI202402\PhpParser\NodeVisitor\NameResolver;
use EasyCI202402\PhpParser\Parser;
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

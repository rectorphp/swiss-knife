<?php

declare(strict_types=1);

namespace Rector\SwissKnife\PhpParser;

use Nette\Utils\FileSystem;
use PhpParser\Node\Stmt;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;

/**
 * Parse file just once
 */
final class CachedPhpParser
{
    /**
     * @var array<string, Stmt[]>
     */
    private array $cachedStmts = [];

    public function __construct(
        private readonly Parser $phpParser
    ) {
    }

    /**
     * @return Stmt[]
     */
    public function parseFile(string $filePath): array
    {
        if (isset($this->cachedStmts[$filePath])) {
            return $this->cachedStmts[$filePath];
        }

        $fileContents = FileSystem::read($filePath);
        $stmts = $this->phpParser->parse($fileContents);

        if (is_array($stmts)) {
            $nodeTraverser = NodeTraverserFactory::create(new NameResolver());
            $nodeTraverser->traverse($stmts);
        }

        $this->cachedStmts[$filePath] = $stmts ?? [];

        return $stmts ?? [];
    }
}

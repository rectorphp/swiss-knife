<?php

declare(strict_types=1);

namespace TomasVotruba\Lemonade\PhpParser\NodeVisitor;

use PhpParser\NodeVisitorAbstract;

final class FilePathNodeVisitor extends NodeVisitorAbstract
{
    public function __construct(
        private readonly string $filePath
    ) {
    }

    public function enterNode(\PhpParser\Node $node)
    {
        $node->setAttribute('file', $this->filePath);
    }
}

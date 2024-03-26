<?php

declare(strict_types=1);

namespace TomasVotruba\Lemonade\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

final class FilePathNodeVisitor extends NodeVisitorAbstract
{
    public function __construct(
        private readonly string $filePath
    ) {
    }

    public function enterNode(Node $node): Node
    {
        $node->setAttribute('file', $this->filePath);

        return $node;
    }
}

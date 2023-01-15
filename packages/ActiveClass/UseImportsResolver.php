<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ActiveClass;

use Nette\Utils\FileSystem;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use Symplify\EasyCI\ActiveClass\NodeDecorator\FullyQualifiedNameNodeDecorator;
use Symplify\EasyCI\ActiveClass\NodeVisitor\UsedClassNodeVisitor;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\EasyCI\Tests\ActiveClass\UseImportsResolver\UseImportsResolverTest
 */
final class UseImportsResolver
{
    public function __construct(
        private readonly Parser $parser,
        private readonly FullyQualifiedNameNodeDecorator $fullyQualifiedNameNodeDecorator,
    ) {
    }

    /**
     * @param string[] $filePaths
     * @return string[]
     *@api
     */
    public function resolveFromFilePaths(array $filePaths): array
    {
        $usedNames = [];

        foreach ($filePaths as $filePath) {
            $usedNames = array_merge($usedNames, $this->resolve($filePath));
        }

        $usedNames = array_unique($usedNames);
        sort($usedNames);

        return $usedNames;
    }

    /**
     * @return string[]
     */
    public function resolve(string $filePath): array
    {
        $fileContents = FileSystem::read($filePath);

        $stmts = $this->parser->parse($fileContents);
        if ($stmts === null) {
            return [];
        }

        $this->fullyQualifiedNameNodeDecorator->decorate($stmts);

        $nodeTraverser = new NodeTraverser();
        $usedClassNodeVisitor = new UsedClassNodeVisitor();
        $nodeTraverser->addVisitor($usedClassNodeVisitor);
        $nodeTraverser->traverse($stmts);

        return $usedClassNodeVisitor->getUsedNames();
    }
}

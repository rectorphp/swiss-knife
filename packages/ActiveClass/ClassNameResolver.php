<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ActiveClass;

use Nette\Utils\FileSystem;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\EasyCI\ActiveClass\NodeDecorator\FullyQualifiedNameNodeDecorator;
use Symplify\EasyCI\ActiveClass\NodeVisitor\ClassNameNodeVisitor;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\EasyCI\Tests\ActiveClass\ClassNameResolver\ClassNameResolverTest
 */
final class ClassNameResolver
{
    public function __construct(
        private readonly Parser $parser,
        private readonly FullyQualifiedNameNodeDecorator $fullyQualifiedNameNodeDecorator
    ) {
    }

    /**
     * @api
     */
    public function resolveFromFromFilePath(string $filePath): ?string
    {
        $fileContents = FileSystem::read($filePath);

        $stmts = $this->parser->parse($fileContents);
        if ($stmts === null) {
            return null;
        }

        $this->fullyQualifiedNameNodeDecorator->decorate($stmts);

        $classNameNodeVisitor = new ClassNameNodeVisitor();
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($classNameNodeVisitor);
        $nodeTraverser->traverse($stmts);

        return $classNameNodeVisitor->getClassName();
    }
}

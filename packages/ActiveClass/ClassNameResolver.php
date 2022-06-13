<?php

declare (strict_types=1);
namespace Symplify\EasyCI\ActiveClass;

use EasyCI20220613\PhpParser\NodeTraverser;
use EasyCI20220613\PhpParser\Parser;
use EasyCI20220613\Symfony\Component\Finder\SplFileInfo;
use Symplify\EasyCI\ActiveClass\NodeDecorator\FullyQualifiedNameNodeDecorator;
use Symplify\EasyCI\ActiveClass\NodeVisitor\ClassNameNodeVisitor;
use EasyCI20220613\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\EasyCI\Tests\ActiveClass\ClassNameResolver\ClassNameResolverTest
 */
final class ClassNameResolver
{
    /**
     * @var \PhpParser\Parser
     */
    private $parser;
    /**
     * @var \Symplify\EasyCI\ActiveClass\NodeDecorator\FullyQualifiedNameNodeDecorator
     */
    private $fullyQualifiedNameNodeDecorator;
    public function __construct(Parser $parser, FullyQualifiedNameNodeDecorator $fullyQualifiedNameNodeDecorator)
    {
        $this->parser = $parser;
        $this->fullyQualifiedNameNodeDecorator = $fullyQualifiedNameNodeDecorator;
    }
    /**
     * @param SmartFileInfo[]|SplFileInfo[] $fileInfos
     * @return string[]
     */
    public function resolveFromFromFileInfos(array $fileInfos) : array
    {
        $classNames = [];
        foreach ($fileInfos as $fileInfo) {
            $className = $this->resolveFromFromFileInfo($fileInfo);
            if ($className === null) {
                continue;
            }
            $classNames[] = $className;
        }
        return $classNames;
    }
    /**
     * @param \Symplify\SmartFileSystem\SmartFileInfo|\Symfony\Component\Finder\SplFileInfo $phpFileInfo
     */
    public function resolveFromFromFileInfo($phpFileInfo) : ?string
    {
        $stmts = $this->parser->parse($phpFileInfo->getContents());
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

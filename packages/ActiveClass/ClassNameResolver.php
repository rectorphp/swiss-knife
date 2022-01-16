<?php

declare (strict_types=1);
namespace EasyCI20220116\Symplify\EasyCI\ActiveClass;

use EasyCI20220116\PhpParser\NodeTraverser;
use EasyCI20220116\PhpParser\Parser;
use EasyCI20220116\Symfony\Component\Finder\SplFileInfo;
use EasyCI20220116\Symplify\EasyCI\ActiveClass\NodeDecorator\FullyQualifiedNameNodeDecorator;
use EasyCI20220116\Symplify\EasyCI\ActiveClass\NodeVisitor\ClassNameNodeVisitor;
use EasyCI20220116\Symplify\SmartFileSystem\SmartFileInfo;
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
    public function __construct(\EasyCI20220116\PhpParser\Parser $parser, \EasyCI20220116\Symplify\EasyCI\ActiveClass\NodeDecorator\FullyQualifiedNameNodeDecorator $fullyQualifiedNameNodeDecorator)
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
        $classNameNodeVisitor = new \EasyCI20220116\Symplify\EasyCI\ActiveClass\NodeVisitor\ClassNameNodeVisitor();
        $nodeTraverser = new \EasyCI20220116\PhpParser\NodeTraverser();
        $nodeTraverser->addVisitor($classNameNodeVisitor);
        $nodeTraverser->traverse($stmts);
        return $classNameNodeVisitor->getClassName();
    }
}

<?php

declare (strict_types=1);
namespace Symplify\EasyCI\ActiveClass;

use EasyCI20220127\PhpParser\NodeTraverser;
use EasyCI20220127\PhpParser\Parser;
use EasyCI20220127\Symfony\Component\Finder\SplFileInfo;
use Symplify\EasyCI\ActiveClass\NodeDecorator\FullyQualifiedNameNodeDecorator;
use Symplify\EasyCI\ActiveClass\NodeVisitor\ClassNameNodeVisitor;
use EasyCI20220127\Symplify\SmartFileSystem\SmartFileInfo;
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
    public function __construct(\EasyCI20220127\PhpParser\Parser $parser, \Symplify\EasyCI\ActiveClass\NodeDecorator\FullyQualifiedNameNodeDecorator $fullyQualifiedNameNodeDecorator)
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
        $classNameNodeVisitor = new \Symplify\EasyCI\ActiveClass\NodeVisitor\ClassNameNodeVisitor();
        $nodeTraverser = new \EasyCI20220127\PhpParser\NodeTraverser();
        $nodeTraverser->addVisitor($classNameNodeVisitor);
        $nodeTraverser->traverse($stmts);
        return $classNameNodeVisitor->getClassName();
    }
}

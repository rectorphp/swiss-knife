<?php

declare (strict_types=1);
namespace Symplify\EasyCI\ActiveClass;

use EasyCI20220130\PhpParser\NodeTraverser;
use EasyCI20220130\PhpParser\Parser;
use Symplify\EasyCI\ActiveClass\NodeDecorator\FullyQualifiedNameNodeDecorator;
use Symplify\EasyCI\ActiveClass\NodeVisitor\UsedClassNodeVisitor;
use EasyCI20220130\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\EasyCI\Tests\ActiveClass\UseImportsResolver\UseImportsResolverTest
 */
final class UseImportsResolver
{
    /**
     * @var \PhpParser\Parser
     */
    private $parser;
    /**
     * @var \Symplify\EasyCI\ActiveClass\NodeDecorator\FullyQualifiedNameNodeDecorator
     */
    private $fullyQualifiedNameNodeDecorator;
    public function __construct(\EasyCI20220130\PhpParser\Parser $parser, \Symplify\EasyCI\ActiveClass\NodeDecorator\FullyQualifiedNameNodeDecorator $fullyQualifiedNameNodeDecorator)
    {
        $this->parser = $parser;
        $this->fullyQualifiedNameNodeDecorator = $fullyQualifiedNameNodeDecorator;
    }
    /**
     * @param SmartFileInfo[] $phpFileInfos
     * @return string[]
     */
    public function resolveFromFileInfos(array $phpFileInfos) : array
    {
        $usedNames = [];
        foreach ($phpFileInfos as $phpFileInfo) {
            $usedNames = \array_merge($usedNames, $this->resolve($phpFileInfo));
        }
        $usedNames = \array_unique($usedNames);
        \sort($usedNames);
        return $usedNames;
    }
    /**
     * @return string[]
     */
    public function resolve(\EasyCI20220130\Symplify\SmartFileSystem\SmartFileInfo $phpFileInfo) : array
    {
        $stmts = $this->parser->parse($phpFileInfo->getContents());
        if ($stmts === null) {
            return [];
        }
        $this->fullyQualifiedNameNodeDecorator->decorate($stmts);
        $nodeTraverser = new \EasyCI20220130\PhpParser\NodeTraverser();
        $usedClassNodeVisitor = new \Symplify\EasyCI\ActiveClass\NodeVisitor\UsedClassNodeVisitor();
        $nodeTraverser->addVisitor($usedClassNodeVisitor);
        $nodeTraverser->traverse($stmts);
        return $usedClassNodeVisitor->getUsedNames();
    }
}

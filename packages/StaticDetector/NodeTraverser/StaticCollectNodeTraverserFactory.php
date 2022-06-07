<?php

declare (strict_types=1);
namespace EasyCI20220607\Symplify\EasyCI\StaticDetector\NodeTraverser;

use EasyCI20220607\PhpParser\NodeVisitor\NameResolver;
use EasyCI20220607\Symplify\EasyCI\StaticDetector\NodeVisitor\FilePathNodeVisitor;
use EasyCI20220607\Symplify\EasyCI\StaticDetector\NodeVisitor\StaticCollectNodeVisitor;
final class StaticCollectNodeTraverserFactory
{
    /**
     * @var \Symplify\EasyCI\StaticDetector\NodeVisitor\StaticCollectNodeVisitor
     */
    private $staticCollectNodeVisitor;
    /**
     * @var \Symplify\EasyCI\StaticDetector\NodeVisitor\FilePathNodeVisitor
     */
    private $filePathNodeVisitor;
    public function __construct(StaticCollectNodeVisitor $staticCollectNodeVisitor, FilePathNodeVisitor $filePathNodeVisitor)
    {
        $this->staticCollectNodeVisitor = $staticCollectNodeVisitor;
        $this->filePathNodeVisitor = $filePathNodeVisitor;
    }
    public function create() : StaticCollectNodeTraverser
    {
        $staticCollectNodeTraverser = new StaticCollectNodeTraverser();
        $staticCollectNodeTraverser->addVisitor(new NameResolver());
        $staticCollectNodeTraverser->addVisitor($this->staticCollectNodeVisitor);
        $staticCollectNodeTraverser->addVisitor($this->filePathNodeVisitor);
        return $staticCollectNodeTraverser;
    }
}

<?php

declare (strict_types=1);
namespace EasyCI20220115\Symplify\EasyCI\StaticDetector\NodeTraverser;

use EasyCI20220115\PhpParser\NodeVisitor\NameResolver;
use EasyCI20220115\Symplify\EasyCI\StaticDetector\NodeVisitor\FilePathNodeVisitor;
use EasyCI20220115\Symplify\EasyCI\StaticDetector\NodeVisitor\StaticCollectNodeVisitor;
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
    public function __construct(\EasyCI20220115\Symplify\EasyCI\StaticDetector\NodeVisitor\StaticCollectNodeVisitor $staticCollectNodeVisitor, \EasyCI20220115\Symplify\EasyCI\StaticDetector\NodeVisitor\FilePathNodeVisitor $filePathNodeVisitor)
    {
        $this->staticCollectNodeVisitor = $staticCollectNodeVisitor;
        $this->filePathNodeVisitor = $filePathNodeVisitor;
    }
    public function create() : \EasyCI20220115\Symplify\EasyCI\StaticDetector\NodeTraverser\StaticCollectNodeTraverser
    {
        $staticCollectNodeTraverser = new \EasyCI20220115\Symplify\EasyCI\StaticDetector\NodeTraverser\StaticCollectNodeTraverser();
        $staticCollectNodeTraverser->addVisitor(new \EasyCI20220115\PhpParser\NodeVisitor\NameResolver());
        $staticCollectNodeTraverser->addVisitor($this->staticCollectNodeVisitor);
        $staticCollectNodeTraverser->addVisitor($this->filePathNodeVisitor);
        return $staticCollectNodeTraverser;
    }
}

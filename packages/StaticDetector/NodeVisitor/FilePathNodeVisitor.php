<?php

declare (strict_types=1);
namespace Symplify\EasyCI\StaticDetector\NodeVisitor;

use EasyCI202301\PhpParser\Node;
use EasyCI202301\PhpParser\NodeVisitorAbstract;
use Symplify\EasyCI\StaticDetector\CurrentProvider\CurrentFileInfoProvider;
use Symplify\EasyCI\StaticDetector\ValueObject\StaticDetectorAttributeKey;
final class FilePathNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var \Symplify\EasyCI\StaticDetector\CurrentProvider\CurrentFileInfoProvider
     */
    private $currentFileInfoProvider;
    public function __construct(CurrentFileInfoProvider $currentFileInfoProvider)
    {
        $this->currentFileInfoProvider = $currentFileInfoProvider;
    }
    public function enterNode(Node $node)
    {
        $smartFileInfo = $this->currentFileInfoProvider->getSmartFileInfo();
        $fileLine = $smartFileInfo->getRelativeFilePathFromCwd() . ':' . $node->getStartLine();
        $node->setAttribute(StaticDetectorAttributeKey::FILE_LINE, $fileLine);
        return null;
    }
}

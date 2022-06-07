<?php

declare (strict_types=1);
namespace EasyCI20220607\Symplify\EasyCI\StaticDetector\NodeVisitor;

use EasyCI20220607\PhpParser\Node;
use EasyCI20220607\PhpParser\NodeVisitorAbstract;
use EasyCI20220607\Symplify\EasyCI\StaticDetector\CurrentProvider\CurrentFileInfoProvider;
use EasyCI20220607\Symplify\EasyCI\StaticDetector\ValueObject\StaticDetectorAttributeKey;
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

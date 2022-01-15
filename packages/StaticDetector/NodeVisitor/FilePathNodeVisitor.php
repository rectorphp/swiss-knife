<?php

declare (strict_types=1);
namespace EasyCI20220115\Symplify\EasyCI\StaticDetector\NodeVisitor;

use EasyCI20220115\PhpParser\Node;
use EasyCI20220115\PhpParser\NodeVisitorAbstract;
use EasyCI20220115\Symplify\EasyCI\StaticDetector\CurrentProvider\CurrentFileInfoProvider;
use EasyCI20220115\Symplify\EasyCI\StaticDetector\ValueObject\StaticDetectorAttributeKey;
final class FilePathNodeVisitor extends \EasyCI20220115\PhpParser\NodeVisitorAbstract
{
    /**
     * @var \Symplify\EasyCI\StaticDetector\CurrentProvider\CurrentFileInfoProvider
     */
    private $currentFileInfoProvider;
    public function __construct(\EasyCI20220115\Symplify\EasyCI\StaticDetector\CurrentProvider\CurrentFileInfoProvider $currentFileInfoProvider)
    {
        $this->currentFileInfoProvider = $currentFileInfoProvider;
    }
    public function enterNode(\EasyCI20220115\PhpParser\Node $node)
    {
        $smartFileInfo = $this->currentFileInfoProvider->getSmartFileInfo();
        $fileLine = $smartFileInfo->getRelativeFilePathFromCwd() . ':' . $node->getStartLine();
        $node->setAttribute(\EasyCI20220115\Symplify\EasyCI\StaticDetector\ValueObject\StaticDetectorAttributeKey::FILE_LINE, $fileLine);
        return null;
    }
}

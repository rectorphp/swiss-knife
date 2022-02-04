<?php

declare (strict_types=1);
namespace Symplify\EasyCI\StaticDetector\NodeVisitor;

use EasyCI20220204\PhpParser\Node;
use EasyCI20220204\PhpParser\NodeVisitorAbstract;
use Symplify\EasyCI\StaticDetector\CurrentProvider\CurrentFileInfoProvider;
use Symplify\EasyCI\StaticDetector\ValueObject\StaticDetectorAttributeKey;
final class FilePathNodeVisitor extends \EasyCI20220204\PhpParser\NodeVisitorAbstract
{
    /**
     * @var \Symplify\EasyCI\StaticDetector\CurrentProvider\CurrentFileInfoProvider
     */
    private $currentFileInfoProvider;
    public function __construct(\Symplify\EasyCI\StaticDetector\CurrentProvider\CurrentFileInfoProvider $currentFileInfoProvider)
    {
        $this->currentFileInfoProvider = $currentFileInfoProvider;
    }
    public function enterNode(\EasyCI20220204\PhpParser\Node $node)
    {
        $smartFileInfo = $this->currentFileInfoProvider->getSmartFileInfo();
        $fileLine = $smartFileInfo->getRelativeFilePathFromCwd() . ':' . $node->getStartLine();
        $node->setAttribute(\Symplify\EasyCI\StaticDetector\ValueObject\StaticDetectorAttributeKey::FILE_LINE, $fileLine);
        return null;
    }
}

<?php

declare (strict_types=1);
namespace Symplify\EasyCI\StaticDetector;

use EasyCI20220429\PhpParser\Parser;
use EasyCI20220429\Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCI\StaticDetector\CurrentProvider\CurrentFileInfoProvider;
use Symplify\EasyCI\StaticDetector\NodeTraverser\StaticCollectNodeTraverser;
use EasyCI20220429\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\EasyCI\Tests\StaticDetector\StaticScanner\StaticScannerTest
 */
final class StaticScanner
{
    /**
     * @var \Symplify\EasyCI\StaticDetector\NodeTraverser\StaticCollectNodeTraverser
     */
    private $staticCollectNodeTraverser;
    /**
     * @var \PhpParser\Parser
     */
    private $parser;
    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @var \Symplify\EasyCI\StaticDetector\CurrentProvider\CurrentFileInfoProvider
     */
    private $currentFileInfoProvider;
    public function __construct(\Symplify\EasyCI\StaticDetector\NodeTraverser\StaticCollectNodeTraverser $staticCollectNodeTraverser, \EasyCI20220429\PhpParser\Parser $parser, \EasyCI20220429\Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle, \Symplify\EasyCI\StaticDetector\CurrentProvider\CurrentFileInfoProvider $currentFileInfoProvider)
    {
        $this->staticCollectNodeTraverser = $staticCollectNodeTraverser;
        $this->parser = $parser;
        $this->symfonyStyle = $symfonyStyle;
        $this->currentFileInfoProvider = $currentFileInfoProvider;
    }
    /**
     * @param SmartFileInfo[] $fileInfos
     */
    public function scanFileInfos(array $fileInfos) : void
    {
        $this->symfonyStyle->note('Looking for static methods and their calls...');
        $stepCount = \count($fileInfos);
        $this->symfonyStyle->progressStart($stepCount);
        foreach ($fileInfos as $fileInfo) {
            $this->currentFileInfoProvider->setCurrentFileInfo($fileInfo);
            $processingMessage = \sprintf('Processing "%s" file', $fileInfo->getRelativeFilePathFromCwd());
            if ($this->symfonyStyle->isDebug()) {
                $this->symfonyStyle->note($processingMessage);
            } else {
                $this->symfonyStyle->progressAdvance();
            }
            // collect static calls
            // collect static class methods
            $this->scanFileInfo($fileInfo);
        }
        $this->symfonyStyle->newLine(2);
        $this->symfonyStyle->success('Scanning done');
        $this->symfonyStyle->newLine(1);
    }
    private function scanFileInfo(\EasyCI20220429\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : void
    {
        $nodes = $this->parser->parse($smartFileInfo->getContents());
        if ($nodes === null) {
            return;
        }
        $this->staticCollectNodeTraverser->traverse($nodes);
    }
}

<?php

declare (strict_types=1);
namespace EasyCI20220524\Symplify\EasyTesting\FixtureSplitter;

use EasyCI20220524\Nette\Utils\Strings;
use EasyCI20220524\Symplify\EasyTesting\ValueObject\FixtureSplit\TrioContent;
use EasyCI20220524\Symplify\EasyTesting\ValueObject\SplitLine;
use EasyCI20220524\Symplify\SmartFileSystem\SmartFileInfo;
use EasyCI20220524\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
/**
 * @api
 */
final class TrioFixtureSplitter
{
    public function splitFileInfo(\EasyCI20220524\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : \EasyCI20220524\Symplify\EasyTesting\ValueObject\FixtureSplit\TrioContent
    {
        $parts = \EasyCI20220524\Nette\Utils\Strings::split($smartFileInfo->getContents(), \EasyCI20220524\Symplify\EasyTesting\ValueObject\SplitLine::SPLIT_LINE_REGEX);
        $this->ensureHasThreeParts($parts, $smartFileInfo);
        return new \EasyCI20220524\Symplify\EasyTesting\ValueObject\FixtureSplit\TrioContent($parts[0], $parts[1], $parts[2]);
    }
    /**
     * @param mixed[] $parts
     */
    private function ensureHasThreeParts(array $parts, \EasyCI20220524\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : void
    {
        if (\count($parts) === 3) {
            return;
        }
        $message = \sprintf('The fixture "%s" should have 3 parts. %d found', $smartFileInfo->getRelativeFilePathFromCwd(), \count($parts));
        throw new \EasyCI20220524\Symplify\SymplifyKernel\Exception\ShouldNotHappenException($message);
    }
}

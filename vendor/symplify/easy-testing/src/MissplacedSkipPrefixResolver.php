<?php

declare (strict_types=1);
namespace EasyCI20220325\Symplify\EasyTesting;

use EasyCI20220325\Nette\Utils\Strings;
use EasyCI20220325\Symplify\EasyTesting\ValueObject\IncorrectAndMissingSkips;
use EasyCI20220325\Symplify\EasyTesting\ValueObject\Prefix;
use EasyCI20220325\Symplify\EasyTesting\ValueObject\SplitLine;
use EasyCI20220325\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\EasyTesting\Tests\MissingSkipPrefixResolver\MissingSkipPrefixResolverTest
 */
final class MissplacedSkipPrefixResolver
{
    /**
     * @param SmartFileInfo[] $fixtureFileInfos
     */
    public function resolve(array $fixtureFileInfos) : \EasyCI20220325\Symplify\EasyTesting\ValueObject\IncorrectAndMissingSkips
    {
        $incorrectSkips = [];
        $missingSkips = [];
        foreach ($fixtureFileInfos as $fixtureFileInfo) {
            $hasNameSkipStart = $this->hasNameSkipStart($fixtureFileInfo);
            $fileContents = $fixtureFileInfo->getContents();
            $hasSplitLine = (bool) \EasyCI20220325\Nette\Utils\Strings::match($fileContents, \EasyCI20220325\Symplify\EasyTesting\ValueObject\SplitLine::SPLIT_LINE_REGEX);
            if ($hasNameSkipStart && $hasSplitLine) {
                $incorrectSkips[] = $fixtureFileInfo;
                continue;
            }
            if (!$hasNameSkipStart && !$hasSplitLine) {
                $missingSkips[] = $fixtureFileInfo;
            }
        }
        return new \EasyCI20220325\Symplify\EasyTesting\ValueObject\IncorrectAndMissingSkips($incorrectSkips, $missingSkips);
    }
    private function hasNameSkipStart(\EasyCI20220325\Symplify\SmartFileSystem\SmartFileInfo $fixtureFileInfo) : bool
    {
        return (bool) \EasyCI20220325\Nette\Utils\Strings::match($fixtureFileInfo->getBasenameWithoutSuffix(), \EasyCI20220325\Symplify\EasyTesting\ValueObject\Prefix::SKIP_PREFIX_REGEX);
    }
}

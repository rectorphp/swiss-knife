<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Config;

use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ClassExtractor
{
    /**
     * @var string
     * @see https://regex101.com/r/1VKOxi/8
     */
    private const CLASS_NAME_REGEX = '#(?<' . self::INDENT_SPACES . '>^\s+)?(.*?)(?<quote>["\']?)\b(?<' . self::CLASS_NAME_PART . '>[A-Za-z](\w+\\\\(\\\\)?)+(\w+))(?<next_char>\\\\|\\\\:|(?&quote))?(?!:)$#m';

    /**
     * @var string
     * @see https://regex101.com/r/1IpNtV/3
     */
    private const STATIC_CALL_CLASS_REGEX = '#(?<quote>["\']?)[\\\\]*(?<class_name>[A-Za-z][\w\\\\]+)::#';

    /**
     * @var string
     */
    private const NEXT_CHAR = 'next_char';

    /**
     * @var string
     */
    private const CLASS_NAME_PART = 'class_name';

    /**
     * @var string
     */
    private const INDENT_SPACES = 'indent_spaces';

    /**
     * @return string[]
     */
    public function extractFromFileInfo(SmartFileInfo $fileInfo): array
    {
        if ($fileInfo->getSuffix() === 'neon') {
            return [];
        }

        $classNames = [];

        $fileContent = $fileInfo->getContents();
        $classNameMatches = Strings::matchAll($fileContent, self::CLASS_NAME_REGEX);

        foreach ($classNameMatches as $classNameMatch) {
            if (isset($classNameMatch[self::NEXT_CHAR]) && ($classNameMatch[self::NEXT_CHAR] === '\\' || $classNameMatch[self::NEXT_CHAR] === '\\:')) {
                // is Symfony autodiscovery → skip
                continue;
            }

            if ($this->shouldSkipArgument($classNameMatch)) {
                continue;
            }

            $classNames[] = $this->extractClassName($fileInfo, $classNameMatch);
        }

        $staticCallsMatches = Strings::matchAll($fileContent, self::STATIC_CALL_CLASS_REGEX);
        foreach ($staticCallsMatches as $staticCallMatch) {
            $classNames[] = $this->extractClassName($fileInfo, $staticCallMatch);
        }

        return $classNames;
    }

    /**
     * @param array<string, string> $match
     */
    private function extractClassName(SmartFileInfo $fileInfo, array $match): string
    {
        if ($fileInfo->getSuffix() === 'twig' && $match['quote'] !== '') {
            return str_replace('\\\\', '\\', $match[self::CLASS_NAME_PART]);
        }

        return $match[self::CLASS_NAME_PART];
    }

    /**
     * @param array<string, mixed> $classNameMatch
     */
    private function shouldSkipArgument(array $classNameMatch): bool
    {
        if (! isset($classNameMatch[self::INDENT_SPACES])) {
            return false;
        }

        // indented argument
        $indentSpaces = $classNameMatch[self::INDENT_SPACES];
        if (substr_count((string) $indentSpaces, "\t") >= 3) {
            return true;
        }

        // in case of spaces
        return substr_count((string) $indentSpaces, ' ') >= 12;
    }
}

<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Resolver;

use Nette\Utils\Strings;
use Rector\SwissKnife\Helpers\ClassNameResolver;
use Rector\SwissKnife\ValueObject\ClassConstMatch;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Resolve all "static::SOME_CONST" calls
 */
final class StaticClassConstResolver
{
    /**
     * @var string
     * @see https://regex101.com/r/aNpvq7/1
     */
    private const STATIC_CONST_CALL_REGEX = '#static::(?<constant_name>[A-Z\_]+)#ms';

    /**
     * @param SplFileInfo[] $phpFileInfos
     * @return ClassConstMatch[]
     */
    public function resolve(array $phpFileInfos): array
    {
        $staticConstMatches = [];
        foreach ($phpFileInfos as $phpFileInfo) {
            $match = Strings::match($phpFileInfo->getContents(), self::STATIC_CONST_CALL_REGEX);
            if ($match === null) {
                continue;
            }

            $fullyQualifiedClassName = ClassNameResolver::resolveFromFileContents($phpFileInfo->getContents());
            if ($fullyQualifiedClassName === null) {
                continue;
            }

            $staticConstMatches[] = new ClassConstMatch($fullyQualifiedClassName, $match['constant_name']);
        }

        return $staticConstMatches;
    }
}

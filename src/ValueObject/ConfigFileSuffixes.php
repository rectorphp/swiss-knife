<?php

declare (strict_types=1);
namespace EasyCI20220116\Symplify\EasyCI\ValueObject;

final class ConfigFileSuffixes
{
    /**
     * @var string[]
     */
    public const SUFFIXES = ['yml', 'yaml', 'neon'];
    public static function provideRegex() : string
    {
        return '#(' . \implode('|', self::SUFFIXES) . ')$#';
    }
}

<?php

declare (strict_types=1);
namespace SwissKnife202601\Entropy\Utils;

use SwissKnife202601\Entropy\Attributes\RelatedTest;
use SwissKnife202601\Entropy\Tests\Utils\StringsTest;
/**
 * @api to be used outside
 */
final class Strings
{
    public static function webalize(string $text) : string
    {
        $text = (string) \preg_replace('/[^\\p{L}\\p{N}]+/u', '-', $text);
        $text = \trim($text, '-');
        return \strtolower($text);
    }
}

<?php

declare (strict_types=1);
namespace SwissKnife202504\PhpParser\Lexer\TokenEmulator;

use SwissKnife202504\PhpParser\PhpVersion;
final class PropertyTokenEmulator extends KeywordEmulator
{
    public function getPhpVersion() : PhpVersion
    {
        return PhpVersion::fromComponents(8, 4);
    }
    public function getKeywordString() : string
    {
        return '__property__';
    }
    public function getKeywordToken() : int
    {
        return \T_PROPERTY_C;
    }
}

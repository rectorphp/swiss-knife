<?php

declare (strict_types=1);
namespace EasyCI20220221\PhpParser\Lexer\TokenEmulator;

use EasyCI20220221\PhpParser\Lexer\Emulative;
final class MatchTokenEmulator extends \EasyCI20220221\PhpParser\Lexer\TokenEmulator\KeywordEmulator
{
    public function getPhpVersion() : string
    {
        return \EasyCI20220221\PhpParser\Lexer\Emulative::PHP_8_0;
    }
    public function getKeywordString() : string
    {
        return 'match';
    }
    public function getKeywordToken() : int
    {
        return \T_MATCH;
    }
}

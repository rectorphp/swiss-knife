<?php

declare (strict_types=1);
namespace EasyCI20220121\PhpParser\Lexer\TokenEmulator;

use EasyCI20220121\PhpParser\Lexer\Emulative;
final class MatchTokenEmulator extends \EasyCI20220121\PhpParser\Lexer\TokenEmulator\KeywordEmulator
{
    public function getPhpVersion() : string
    {
        return \EasyCI20220121\PhpParser\Lexer\Emulative::PHP_8_0;
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

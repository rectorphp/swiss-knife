<?php

declare (strict_types=1);
namespace EasyCI20220609\PhpParser\Lexer\TokenEmulator;

use EasyCI20220609\PhpParser\Lexer\Emulative;
final class ReadonlyTokenEmulator extends KeywordEmulator
{
    public function getPhpVersion() : string
    {
        return Emulative::PHP_8_1;
    }
    public function getKeywordString() : string
    {
        return 'readonly';
    }
    public function getKeywordToken() : int
    {
        return \T_READONLY;
    }
}

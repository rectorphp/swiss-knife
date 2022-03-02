<?php

declare (strict_types=1);
namespace EasyCI20220302\PhpParser\Lexer\TokenEmulator;

use EasyCI20220302\PhpParser\Lexer\Emulative;
final class ReadonlyTokenEmulator extends \EasyCI20220302\PhpParser\Lexer\TokenEmulator\KeywordEmulator
{
    public function getPhpVersion() : string
    {
        return \EasyCI20220302\PhpParser\Lexer\Emulative::PHP_8_1;
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

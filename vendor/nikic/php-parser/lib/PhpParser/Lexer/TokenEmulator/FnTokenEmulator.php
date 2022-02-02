<?php

declare (strict_types=1);
namespace EasyCI20220202\PhpParser\Lexer\TokenEmulator;

use EasyCI20220202\PhpParser\Lexer\Emulative;
final class FnTokenEmulator extends \EasyCI20220202\PhpParser\Lexer\TokenEmulator\KeywordEmulator
{
    public function getPhpVersion() : string
    {
        return \EasyCI20220202\PhpParser\Lexer\Emulative::PHP_7_4;
    }
    public function getKeywordString() : string
    {
        return 'fn';
    }
    public function getKeywordToken() : int
    {
        return \T_FN;
    }
}

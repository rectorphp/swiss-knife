<?php

declare (strict_types=1);
namespace EasyCI20220607\Symplify\Astral\PhpDocParser\StaticFactory;

use EasyCI20220607\PHPStan\PhpDocParser\Lexer\Lexer;
use EasyCI20220607\PHPStan\PhpDocParser\Parser\ConstExprParser;
use EasyCI20220607\PHPStan\PhpDocParser\Parser\PhpDocParser;
use EasyCI20220607\PHPStan\PhpDocParser\Parser\TypeParser;
use EasyCI20220607\Symplify\Astral\PhpDocParser\SimplePhpDocParser;
/**
 * @api
 */
final class SimplePhpDocParserStaticFactory
{
    public static function create() : SimplePhpDocParser
    {
        $phpDocParser = new PhpDocParser(new TypeParser(), new ConstExprParser());
        return new SimplePhpDocParser($phpDocParser, new Lexer());
    }
}

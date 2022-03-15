<?php

declare (strict_types=1);
namespace EasyCI20220315\Symplify\Astral\PhpParser;

use EasyCI20220315\PhpParser\Lexer\Emulative;
use EasyCI20220315\PhpParser\NodeVisitor\NameResolver;
use EasyCI20220315\PhpParser\Parser;
use EasyCI20220315\PhpParser\ParserFactory;
use EasyCI20220315\PHPStan\Parser\CachedParser;
use EasyCI20220315\PHPStan\Parser\SimpleParser;
/**
 * Based on PHPStan-based PHP-Parser best practices:
 *
 * @see https://github.com/rectorphp/rector/issues/6744#issuecomment-950282826
 * @see https://github.com/phpstan/phpstan-src/blob/99e4ae0dced58fe0be7a7aec3168a5e9d639240a/conf/config.neon#L1669-L1691
 */
final class SmartPhpParserFactory
{
    public function create() : \EasyCI20220315\Symplify\Astral\PhpParser\SmartPhpParser
    {
        $nativePhpParser = $this->createNativePhpParser();
        $cachedParser = $this->createPHPStanParser($nativePhpParser);
        return new \EasyCI20220315\Symplify\Astral\PhpParser\SmartPhpParser($cachedParser);
    }
    private function createNativePhpParser() : \EasyCI20220315\PhpParser\Parser
    {
        $parserFactory = new \EasyCI20220315\PhpParser\ParserFactory();
        $lexerEmulative = new \EasyCI20220315\PhpParser\Lexer\Emulative();
        return $parserFactory->create(\EasyCI20220315\PhpParser\ParserFactory::PREFER_PHP7, $lexerEmulative);
    }
    private function createPHPStanParser(\EasyCI20220315\PhpParser\Parser $parser) : \EasyCI20220315\PHPStan\Parser\CachedParser
    {
        $nameResolver = new \EasyCI20220315\PhpParser\NodeVisitor\NameResolver();
        $simpleParser = new \EasyCI20220315\PHPStan\Parser\SimpleParser($parser, $nameResolver);
        return new \EasyCI20220315\PHPStan\Parser\CachedParser($simpleParser, 1024);
    }
}

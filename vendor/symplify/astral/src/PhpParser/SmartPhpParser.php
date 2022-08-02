<?php

declare (strict_types=1);
namespace EasyCI202208\Symplify\Astral\PhpParser;

use EasyCI202208\PhpParser\Node\Stmt;
use EasyCI202208\PHPStan\Parser\Parser;
/**
 * @see \Symplify\Astral\PhpParser\SmartPhpParserFactory
 *
 * @api
 */
final class SmartPhpParser
{
    /**
     * @var \PHPStan\Parser\Parser
     */
    private $parser;
    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }
    /**
     * @return Stmt[]
     */
    public function parseFile(string $file) : array
    {
        return $this->parser->parseFile($file);
    }
    /**
     * @return Stmt[]
     */
    public function parseString(string $sourceCode) : array
    {
        return $this->parser->parseString($sourceCode);
    }
}

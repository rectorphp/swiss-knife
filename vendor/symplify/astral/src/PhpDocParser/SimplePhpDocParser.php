<?php

declare (strict_types=1);
namespace EasyCI20220429\Symplify\Astral\PhpDocParser;

use EasyCI20220429\PhpParser\Comment\Doc;
use EasyCI20220429\PhpParser\Node;
use EasyCI20220429\PHPStan\PhpDocParser\Lexer\Lexer;
use EasyCI20220429\PHPStan\PhpDocParser\Parser\PhpDocParser;
use EasyCI20220429\PHPStan\PhpDocParser\Parser\TokenIterator;
use EasyCI20220429\Symplify\Astral\PhpDocParser\ValueObject\Ast\PhpDoc\SimplePhpDocNode;
/**
 * @see \Symplify\Astral\Tests\PhpDocParser\SimplePhpDocParser\SimplePhpDocParserTest
 */
final class SimplePhpDocParser
{
    /**
     * @var \PHPStan\PhpDocParser\Parser\PhpDocParser
     */
    private $phpDocParser;
    /**
     * @var \PHPStan\PhpDocParser\Lexer\Lexer
     */
    private $lexer;
    public function __construct(\EasyCI20220429\PHPStan\PhpDocParser\Parser\PhpDocParser $phpDocParser, \EasyCI20220429\PHPStan\PhpDocParser\Lexer\Lexer $lexer)
    {
        $this->phpDocParser = $phpDocParser;
        $this->lexer = $lexer;
    }
    public function parseNode(\EasyCI20220429\PhpParser\Node $node) : ?\EasyCI20220429\Symplify\Astral\PhpDocParser\ValueObject\Ast\PhpDoc\SimplePhpDocNode
    {
        $docComment = $node->getDocComment();
        if (!$docComment instanceof \EasyCI20220429\PhpParser\Comment\Doc) {
            return null;
        }
        return $this->parseDocBlock($docComment->getText());
    }
    public function parseDocBlock(string $docBlock) : \EasyCI20220429\Symplify\Astral\PhpDocParser\ValueObject\Ast\PhpDoc\SimplePhpDocNode
    {
        $tokens = $this->lexer->tokenize($docBlock);
        $tokenIterator = new \EasyCI20220429\PHPStan\PhpDocParser\Parser\TokenIterator($tokens);
        $phpDocNode = $this->phpDocParser->parse($tokenIterator);
        return new \EasyCI20220429\Symplify\Astral\PhpDocParser\ValueObject\Ast\PhpDoc\SimplePhpDocNode($phpDocNode->children);
    }
}

<?php

declare (strict_types=1);
namespace EasyCI20220524\PHPStan\PhpDocParser\Parser;

use EasyCI20220524\PHPStan\PhpDocParser\Ast;
use EasyCI20220524\PHPStan\PhpDocParser\Lexer\Lexer;
use function strtolower;
use function trim;
class ConstExprParser
{
    public function parse(\EasyCI20220524\PHPStan\PhpDocParser\Parser\TokenIterator $tokens, bool $trimStrings = \false) : \EasyCI20220524\PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprNode
    {
        if ($tokens->isCurrentTokenType(\EasyCI20220524\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_FLOAT)) {
            $value = $tokens->currentTokenValue();
            $tokens->next();
            return new \EasyCI20220524\PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprFloatNode($value);
        }
        if ($tokens->isCurrentTokenType(\EasyCI20220524\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_INTEGER)) {
            $value = $tokens->currentTokenValue();
            $tokens->next();
            return new \EasyCI20220524\PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprIntegerNode($value);
        }
        if ($tokens->isCurrentTokenType(\EasyCI20220524\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_SINGLE_QUOTED_STRING)) {
            $value = $tokens->currentTokenValue();
            if ($trimStrings) {
                $value = \trim($tokens->currentTokenValue(), "'");
            }
            $tokens->next();
            return new \EasyCI20220524\PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprStringNode($value);
        } elseif ($tokens->isCurrentTokenType(\EasyCI20220524\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_DOUBLE_QUOTED_STRING)) {
            $value = $tokens->currentTokenValue();
            if ($trimStrings) {
                $value = \trim($tokens->currentTokenValue(), '"');
            }
            $tokens->next();
            return new \EasyCI20220524\PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprStringNode($value);
        } elseif ($tokens->isCurrentTokenType(\EasyCI20220524\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_IDENTIFIER)) {
            $identifier = $tokens->currentTokenValue();
            $tokens->next();
            switch (\strtolower($identifier)) {
                case 'true':
                    return new \EasyCI20220524\PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprTrueNode();
                case 'false':
                    return new \EasyCI20220524\PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprFalseNode();
                case 'null':
                    return new \EasyCI20220524\PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprNullNode();
                case 'array':
                    $tokens->consumeTokenType(\EasyCI20220524\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_OPEN_PARENTHESES);
                    return $this->parseArray($tokens, \EasyCI20220524\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_CLOSE_PARENTHESES);
            }
            if ($tokens->tryConsumeTokenType(\EasyCI20220524\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_DOUBLE_COLON)) {
                $classConstantName = '';
                $lastType = null;
                while (\true) {
                    if ($lastType !== \EasyCI20220524\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_IDENTIFIER && $tokens->currentTokenType() === \EasyCI20220524\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_IDENTIFIER) {
                        $classConstantName .= $tokens->currentTokenValue();
                        $tokens->consumeTokenType(\EasyCI20220524\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_IDENTIFIER);
                        $lastType = \EasyCI20220524\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_IDENTIFIER;
                        continue;
                    }
                    if ($lastType !== \EasyCI20220524\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_WILDCARD && $tokens->tryConsumeTokenType(\EasyCI20220524\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_WILDCARD)) {
                        $classConstantName .= '*';
                        $lastType = \EasyCI20220524\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_WILDCARD;
                        if ($tokens->getSkippedHorizontalWhiteSpaceIfAny() !== '') {
                            break;
                        }
                        continue;
                    }
                    if ($lastType === null) {
                        // trigger parse error if nothing valid was consumed
                        $tokens->consumeTokenType(\EasyCI20220524\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_WILDCARD);
                    }
                    break;
                }
                return new \EasyCI20220524\PHPStan\PhpDocParser\Ast\ConstExpr\ConstFetchNode($identifier, $classConstantName);
            }
            return new \EasyCI20220524\PHPStan\PhpDocParser\Ast\ConstExpr\ConstFetchNode('', $identifier);
        } elseif ($tokens->tryConsumeTokenType(\EasyCI20220524\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_OPEN_SQUARE_BRACKET)) {
            return $this->parseArray($tokens, \EasyCI20220524\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_CLOSE_SQUARE_BRACKET);
        }
        throw new \EasyCI20220524\PHPStan\PhpDocParser\Parser\ParserException($tokens->currentTokenValue(), $tokens->currentTokenType(), $tokens->currentTokenOffset(), \EasyCI20220524\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_IDENTIFIER);
    }
    private function parseArray(\EasyCI20220524\PHPStan\PhpDocParser\Parser\TokenIterator $tokens, int $endToken) : \EasyCI20220524\PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprArrayNode
    {
        $items = [];
        if (!$tokens->tryConsumeTokenType($endToken)) {
            do {
                $items[] = $this->parseArrayItem($tokens);
            } while ($tokens->tryConsumeTokenType(\EasyCI20220524\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_COMMA) && !$tokens->isCurrentTokenType($endToken));
            $tokens->consumeTokenType($endToken);
        }
        return new \EasyCI20220524\PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprArrayNode($items);
    }
    private function parseArrayItem(\EasyCI20220524\PHPStan\PhpDocParser\Parser\TokenIterator $tokens) : \EasyCI20220524\PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprArrayItemNode
    {
        $expr = $this->parse($tokens);
        if ($tokens->tryConsumeTokenType(\EasyCI20220524\PHPStan\PhpDocParser\Lexer\Lexer::TOKEN_DOUBLE_ARROW)) {
            $key = $expr;
            $value = $this->parse($tokens);
        } else {
            $key = null;
            $value = $expr;
        }
        return new \EasyCI20220524\PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprArrayItemNode($key, $value);
    }
}

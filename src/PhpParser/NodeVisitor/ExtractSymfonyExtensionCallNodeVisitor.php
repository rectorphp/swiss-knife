<?php

declare (strict_types=1);
namespace Rector\SwissKnife\PhpParser\NodeVisitor;

use SwissKnife202508\PhpParser\Node;
use SwissKnife202508\PhpParser\Node\Expr\MethodCall;
use SwissKnife202508\PhpParser\Node\Identifier;
use SwissKnife202508\PhpParser\Node\Stmt\Expression;
use SwissKnife202508\PhpParser\NodeVisitorAbstract;
final class ExtractSymfonyExtensionCallNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    private const EXTENSION_METHOD_NAME = 'extension';
    /**
     * @var MethodCall[]
     */
    private $extensionMethodCalls = [];
    public function enterNode(Node $node) : ?int
    {
        if (!$node instanceof Expression) {
            return null;
        }
        if (!$node->expr instanceof MethodCall) {
            return null;
        }
        $methodCall = $node->expr;
        if (!$methodCall->name instanceof Identifier) {
            return null;
        }
        $methodName = $methodCall->name->toString();
        if ($methodName !== self::EXTENSION_METHOD_NAME) {
            return null;
        }
        $this->extensionMethodCalls[] = $methodCall;
        return self::REMOVE_NODE;
    }
    /**
     * @return MethodCall[]
     */
    public function getExtensionMethodCalls() : array
    {
        return $this->extensionMethodCalls;
    }
}

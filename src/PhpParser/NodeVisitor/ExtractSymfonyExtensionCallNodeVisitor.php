<?php

declare(strict_types=1);

namespace Rector\SwissKnife\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeVisitorAbstract;

final class ExtractSymfonyExtensionCallNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    private const EXTENSION_METHOD_NAME = 'extension';

    /**
     * @var MethodCall[]
     */
    private array $extensionMethodCalls = [];

    public function enterNode(Node $node): int|null
    {
        if (! $node instanceof Expression) {
            return null;
        }

        if (! $node->expr instanceof MethodCall) {
            return null;
        }

        $methodCall = $node->expr;
        if (! $methodCall->name instanceof Identifier) {
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
    public function getExtensionMethodCalls(): array
    {
        return $this->extensionMethodCalls;
    }
}

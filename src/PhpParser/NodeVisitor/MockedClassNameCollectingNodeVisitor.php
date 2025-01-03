<?php

declare(strict_types=1);

namespace Rector\SwissKnife\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\NodeVisitorAbstract;

final class MockedClassNameCollectingNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private array $mockedClassNames = [];

    public function enterNode(Node $node): ?Node
    {
        if (! $node instanceof MethodCall && ! $node instanceof StaticCall) {
            return null;
        }

        // method call :)
        if (! $node->name instanceof Identifier) {
            return null;
        }

        $methodName = $node->name->toString();
        $mockMethodNames = [
            'createMock',
            'createPartialMock',
            'getMock',
            'getMockBuilder',
            'mock',
        ];
        if (! in_array($methodName, $mockMethodNames, true)) {
            return null;
        }

        $mockedClassArg = $node->getArgs()[0] ?? null;
        if (! $mockedClassArg instanceof Arg) {
            return null;
        }

        // get class name
        if ($mockedClassArg->value instanceof ClassConstFetch) {
            $mockedClass = $mockedClassArg->value->class;
            if ($mockedClass instanceof Name) {
                $this->mockedClassNames[] = $mockedClass->toString();
            }
        }

        return $node;
    }

    /**
     * @return string[]
     */
    public function getMockedClassNames(): array
    {
        $uniqueMockedClassNames = array_unique($this->mockedClassNames);
        sort($uniqueMockedClassNames);

        return $uniqueMockedClassNames;
    }
}

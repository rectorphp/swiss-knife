<?php

declare (strict_types=1);
namespace Rector\SwissKnife\PhpParser\NodeVisitor;

use SwissKnife202412\PhpParser\Node;
use SwissKnife202412\PhpParser\Node\Arg;
use SwissKnife202412\PhpParser\Node\Expr\ClassConstFetch;
use SwissKnife202412\PhpParser\Node\Expr\MethodCall;
use SwissKnife202412\PhpParser\Node\Expr\StaticCall;
use SwissKnife202412\PhpParser\Node\Identifier;
use SwissKnife202412\PhpParser\Node\Name;
use SwissKnife202412\PhpParser\NodeVisitorAbstract;
final class MockedClassNameCollectingNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private $mockedClassNames = [];
    public function enterNode(Node $node) : ?Node
    {
        if (!$node instanceof MethodCall && !$node instanceof StaticCall) {
            return null;
        }
        // method call :)
        if (!$node->name instanceof Identifier) {
            return null;
        }
        $methodName = $node->name->toString();
        if (!\in_array($methodName, ['getMock', 'createMock', 'mock', 'getMockBuilder'], \true)) {
            return null;
        }
        $mockedClassArg = $node->getArgs()[0] ?? null;
        if (!$mockedClassArg instanceof Arg) {
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
    public function getMockedClassNames() : array
    {
        $uniqueMockedClassNames = \array_unique($this->mockedClassNames);
        \sort($uniqueMockedClassNames);
        return $uniqueMockedClassNames;
    }
}

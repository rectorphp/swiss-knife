<?php

declare (strict_types=1);
namespace Rector\SwissKnife\PhpParser\NodeVisitor;

use SwissKnife202506\PhpParser\Node;
use SwissKnife202506\PhpParser\Node\Arg;
use SwissKnife202506\PhpParser\Node\Expr\ClassConstFetch;
use SwissKnife202506\PhpParser\Node\Expr\MethodCall;
use SwissKnife202506\PhpParser\Node\Expr\StaticCall;
use SwissKnife202506\PhpParser\Node\Identifier;
use SwissKnife202506\PhpParser\Node\Name;
use SwissKnife202506\PhpParser\NodeVisitorAbstract;
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
        $mockMethodNames = [
            'createMock',
            // https://github.com/sebastianbergmann/phpunit/blob/d72b735d34bbff2065cef80653cafbe31cb45ba0/src/Framework/TestCase.php#L1177
            'createPartialMock',
            // https://github.com/sebastianbergmann/phpunit/blob/d72b735d34bbff2065cef80653cafbe31cb45ba0/src/Framework/TestCase.php#L1257
            'getMock',
            // https://github.com/sebastianbergmann/phpunit/blob/d72b735d34bbff2065cef80653cafbe31cb45ba0/src/Framework/MockObject/MockBuilder.php#L86
            'getMockBuilder',
            // https://github.com/sebastianbergmann/phpunit/blob/d72b735d34bbff2065cef80653cafbe31cb45ba0/src/Framework/TestCase.php#L1095
            'mock',
            // https://github.com/mockery/mockery/blob/73a9714716f87510a7c2add9931884188e657541/library/Mockery.php#L475, https://github.com/laravel/framework/blob/4ca4a16772b2e89233b3606badefae34003e1538/src/Illuminate/Foundation/Testing/Concerns/InteractsWithContainer.php#L69
            'partialMock',
            // https://github.com/laravel/framework/blob/4ca4a16772b2e89233b3606badefae34003e1538/src/Illuminate/Foundation/Testing/Concerns/InteractsWithContainer.php#L81
            'spy',
        ];
        if (!\in_array($methodName, $mockMethodNames, \true)) {
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

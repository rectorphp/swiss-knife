<?php

declare(strict_types=1);

namespace TomasVotruba\Lemonade\NodeFinder;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeFinder;
use Symfony\Component\Finder\SplFileInfo;
use TomasVotruba\Lemonade\PhpParser\CachedPhpParser;

final class ServiceMethodCallsFinder
{
    public function __construct(
        private readonly NodeFinder $nodeFinder,
        private readonly CachedPhpParser $cachedPhpParser,
    ) {
    }

    /**
     * @param SplFileInfo[] $fileInfos
     * @return MethodCall[]
     */
    public function findSetMethodCalls(array $fileInfos): array
    {
        /** @var MethodCall[] $bareSetMethodCalls */
        $bareSetMethodCalls = [];

        foreach ($fileInfos as $serviceConfigFileInfo) {
            $stmts = $this->cachedPhpParser->parseFile($serviceConfigFileInfo->getRealPath());

            $currentBareSetMethodCalls = $this->findBareSetServiceMethodCalls($stmts);
            $bareSetMethodCalls = array_merge($bareSetMethodCalls, $currentBareSetMethodCalls);
        }

        return $bareSetMethodCalls;
    }

    /**
     * @param SplFileInfo[] $fileInfos
     * @return MethodCall[]
     */
    public function findLoadMethodCalls(array $fileInfos): array
    {
        /** @var MethodCall[] $loadMethodCalls */
        $loadMethodCalls = [];

        foreach ($fileInfos as $fileInfo) {
            $stmts = $this->cachedPhpParser->parseFile($fileInfo->getRealPath());

            $currentLoadMethodCalls = $this->findBareLoadMethodCalls($stmts);
            $loadMethodCalls = array_merge($loadMethodCalls, $currentLoadMethodCalls);
        }

        return $loadMethodCalls;
    }

    /**
     * @param Stmt[] $stmts
     * @return array<MethodCall>
     */
    private function findBareSetServiceMethodCalls(array $stmts): array
    {
        /** @var Expression[] $expressions */
        $expressions = $this->nodeFinder->find($stmts, function (Node $node): bool {
            if (! $node instanceof Expression) {
                return false;
            }

            if (! $node->expr instanceof MethodCall) {
                return false;
            }

            $methodCall = $node->expr;
            if (! $methodCall->name instanceof Identifier) {
                return false;
            }

            if ($methodCall->name->name !== 'set') {
                return false;
            }

            if (! $methodCall->var instanceof Variable) {
                return false;
            }

            // must have exactly one argument
            if (count($methodCall->getArgs()) !== 1) {
                return false;
            }

            return true;
        });

        /** @var MethodCall[] $methodCalls */
        $methodCalls = [];
        foreach ($expressions as $expression) {
            $methodCalls[] = $expression->expr;
        }

        return $methodCalls;
    }

    /**
     * @param Stmt[] $stmts
     * @return MethodCall[]
     */
    private function findBareLoadMethodCalls(array $stmts): array
    {
        return $this->nodeFinder->find($stmts, function (Node $node) {
            if (! $node instanceof MethodCall) {
                return false;
            }

            if (! $node->name instanceof Identifier) {
                return false;
            }

            return $node->name->name === 'load';
        });
    }
}

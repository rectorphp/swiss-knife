<?php

declare(strict_types=1);

namespace Rector\SwissKnife\PhpParser\NodeFactory;

use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\DeclareDeclare;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Nop;
use PhpParser\Node\Stmt\Return_;
use Rector\SwissKnife\Enum\SymfonyClass;

final class SplitConfigClosureFactory
{
    /**
     * @return Stmt[]
     */
    public function createStmts(MethodCall $extensionMethodCall): array
    {
        $strictTypesDeclare = new Declare_([new DeclareDeclare('strict_types', new LNumber(1))]);

        $closure = new Closure();
        $closure->stmts[] = new Expression($extensionMethodCall);
        $closure->returnType = new Identifier('void');
        $closure->params[] = new Param(new Variable('containerConfigurator'), null, new FullyQualified(
            SymfonyClass::CONTAINER_CONFIGURATOR_CLASS
        ));

        $return = new Return_($closure);

        return [$strictTypesDeclare, new Nop(), $return];
    }
}

<?php

declare (strict_types=1);
namespace Rector\SwissKnife\PhpParser\NodeFactory;

use SwissKnife202507\PhpParser\Node\Expr\Closure;
use SwissKnife202507\PhpParser\Node\Expr\MethodCall;
use SwissKnife202507\PhpParser\Node\Expr\Variable;
use SwissKnife202507\PhpParser\Node\Identifier;
use SwissKnife202507\PhpParser\Node\Name\FullyQualified;
use SwissKnife202507\PhpParser\Node\Param;
use SwissKnife202507\PhpParser\Node\Scalar\Int_;
use SwissKnife202507\PhpParser\Node\Stmt;
use SwissKnife202507\PhpParser\Node\Stmt\Declare_;
use SwissKnife202507\PhpParser\Node\Stmt\DeclareDeclare;
use SwissKnife202507\PhpParser\Node\Stmt\Expression;
use SwissKnife202507\PhpParser\Node\Stmt\Nop;
use SwissKnife202507\PhpParser\Node\Stmt\Return_;
use Rector\SwissKnife\Enum\SymfonyClass;
final class SplitConfigClosureFactory
{
    /**
     * @return Stmt[]
     */
    public function createStmts(MethodCall $extensionMethodCall) : array
    {
        $strictTypesDeclare = new Declare_([new DeclareDeclare('strict_types', new Int_(1))]);
        $closure = new Closure();
        $closure->stmts[] = new Expression($extensionMethodCall);
        $closure->returnType = new Identifier('void');
        $closure->params[] = new Param(new Variable('containerConfigurator'), null, new FullyQualified(SymfonyClass::CONTAINER_CONFIGURATOR_CLASS));
        $return = new Return_($closure);
        return [$strictTypesDeclare, new Nop(), $return];
    }
}

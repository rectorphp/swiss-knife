<?php

declare(strict_types=1);

namespace Rector\SwissKnife\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\MagicConst\Dir;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeVisitorAbstract;

final class AddImportConfigMethodCallNodeVisitor extends NodeVisitorAbstract
{
    public function __construct(
        private readonly string $outputDirectory
    ) {
    }

    public function enterNode(Node $node): int|null
    {
        if (! $node instanceof Closure) {
            return null;
        }

        if (count($node->params) !== 1) {
            return null;
        }

        $configDirectoryPathString = new String_($this->outputDirectory . '/*');
        $concat = new Concat(new Dir(), $configDirectoryPathString);

        $importMethodCall = new MethodCall(new Variable('containerConfigurator'), 'import', [new Arg($concat)]);
        $node->stmts[] = new Expression($importMethodCall);

        return self::STOP_TRAVERSAL;
    }
}

<?php

declare (strict_types=1);
namespace Rector\SwissKnife\PhpParser\NodeVisitor;

use SwissKnife202502\PhpParser\Node;
use SwissKnife202502\PhpParser\Node\Arg;
use SwissKnife202502\PhpParser\Node\Expr\BinaryOp\Concat;
use SwissKnife202502\PhpParser\Node\Expr\Closure;
use SwissKnife202502\PhpParser\Node\Expr\MethodCall;
use SwissKnife202502\PhpParser\Node\Expr\Variable;
use SwissKnife202502\PhpParser\Node\Scalar\MagicConst\Dir;
use SwissKnife202502\PhpParser\Node\Scalar\String_;
use SwissKnife202502\PhpParser\Node\Stmt\Expression;
use SwissKnife202502\PhpParser\NodeVisitorAbstract;
final class AddImportConfigMethodCallNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @readonly
     * @var string
     */
    private $outputDirectory;
    public function __construct(string $outputDirectory)
    {
        $this->outputDirectory = $outputDirectory;
    }
    public function enterNode(Node $node) : ?int
    {
        if (!$node instanceof Closure) {
            return null;
        }
        if (\count($node->params) !== 1) {
            return null;
        }
        $configDirectoryPathString = new String_($this->outputDirectory . '/*');
        $concat = new Concat(new Dir(), $configDirectoryPathString);
        $importMethodCall = new MethodCall(new Variable('containerConfigurator'), 'import', [new Arg($concat)]);
        $node->stmts[] = new Expression($importMethodCall);
        return self::STOP_TRAVERSAL;
    }
}

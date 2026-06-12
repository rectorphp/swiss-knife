<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\PhpParser\NodeFactory;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Expr\Variable;
use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\Enum\SymfonyClass;
use Rector\SwissKnife\PhpParser\NodeFactory\SplitConfigClosureFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

final class SplitConfigClosureFactoryTest extends TestCase
{
    public function testCreateStmts(): void
    {
        $extensionMethodCall = new MethodCall(
            new Variable('containerConfigurator'),
            new Identifier('extension'),
            [new Arg(new String_('framework'))]
        );

        $splitConfigClosureFactory = new SplitConfigClosureFactory();
        $stmts = $splitConfigClosureFactory->createStmts($extensionMethodCall);

        $this->assertCount(3, $stmts);

        $prettyPrinter = new \PhpParser\PrettyPrinter\Standard();
        $printed = $prettyPrinter->prettyPrintFile($stmts);

        $this->assertStringContainsString('strict_types=1', $printed);
        $this->assertStringContainsString(ContainerConfigurator::class, $printed);
        $this->assertStringContainsString(SymfonyClass::CONTAINER_CONFIGURATOR_CLASS, $printed);
    }
}

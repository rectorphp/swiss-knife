<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\PhpParser\NodeVisitor\FindClassConstFetchNodeVisitor;

use Entropy\Console\Output\OutputColorizer;
use Entropy\Console\Output\ProgressBar;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\Exception\NotImplementedYetException;
use Rector\SwissKnife\Finder\PhpFilesFinder;
use Rector\SwissKnife\PhpParser\CachedPhpParser;
use Rector\SwissKnife\PhpParser\Finder\ClassConstantFetchFinder;
use Rector\SwissKnife\PhpParser\NodeTraverserFactory;
use Rector\SwissKnife\PhpParser\NodeVisitor\FindClassConstFetchNodeVisitor;
use Rector\SwissKnife\Testing\Printer\PHPUnitXmlPrinter;
use Rector\SwissKnife\ValueObject\ClassConstantFetch\ExternalClassAccessConstantFetch;

final class FindClassConstFetchNodeVisitorTest extends TestCase
{
    public function testVendorClassIsSkipped(): void
    {
        $visitor = new FindClassConstFetchNodeVisitor();
        $nodeTraverser = NodeTraverserFactory::create($visitor);

        $parser = (new ParserFactory())->createForNewestSupportedVersion();
        $code = <<<'PHP'
<?php
namespace TestVendorSkip;
final class UsingVendor
{
    public function run(): string
    {
        return \PHPUnit\Framework\TestCase::class;
    }
}
PHP;
        $stmts = $parser->parse($code);
        $this->assertNotNull($stmts);

        $nodeTraverser->traverse($stmts);

        $this->assertSame([], $visitor->getClassConstantFetches());
    }

    public function testExternalClassFetch(): void
    {
        require_once __DIR__ . '/../../Finder/ClassConstantFetchFinder/Fixture/Standard/AnotherClassWithConstant.php';

        $visitor = new FindClassConstFetchNodeVisitor();
        $nodeTraverser = NodeTraverserFactory::create($visitor);

        $parser = (new ParserFactory())->createForNewestSupportedVersion();
        $code = <<<'PHP'
<?php
namespace TestExternal;
final class User
{
    public function run(): string
    {
        return \Rector\SwissKnife\Tests\PhpParser\Finder\ClassConstantFetchFinder\Fixture\Standard\AnotherClassWithConstant::ANOTHER_CONSTANT;
    }
}
PHP;
        $stmts = $parser->parse($code);
        $this->assertNotNull($stmts);

        $nodeTraverser->traverse($stmts);

        $fetches = $visitor->getClassConstantFetches();
        $this->assertCount(1, $fetches);
        $this->assertInstanceOf(ExternalClassAccessConstantFetch::class, $fetches[0]);
    }

    public function testNotImplementedYetException(): void
    {
        $this->expectException(NotImplementedYetException::class);

        $visitor = new FindClassConstFetchNodeVisitor();
        $nodeTraverser = NodeTraverserFactory::create($visitor);

        $parser = (new ParserFactory())->createForNewestSupportedVersion();
        $code = <<<'PHP'
<?php
namespace TestMissing;
final class User
{
    public function run(): string
    {
        return MissingClass::FOO;
    }
}
PHP;
        $stmts = $parser->parse($code);
        $nodeTraverser->traverse($stmts);
    }
}

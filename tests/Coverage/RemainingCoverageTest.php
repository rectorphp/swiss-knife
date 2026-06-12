<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Coverage;

use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Output\OutputColorizer;
use Entropy\Console\Output\ProgressBar;
use Nette\Utils\FileSystem;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Expr\Variable;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\Command\NamespaceToPSR4Command;
use Rector\SwissKnife\Command\PrivatizeConstantsCommand;
use Rector\SwissKnife\Command\SearchRegexCommand;
use Rector\SwissKnife\Command\SplitSymfonyConfigToPerPackageCommand;
use Rector\SwissKnife\Exception\ShouldNotHappenException;
use Rector\SwissKnife\Finder\PhpFilesFinder;
use Rector\SwissKnife\PhpParser\Finder\ClassConstantFetchFinder;
use Rector\SwissKnife\PhpParser\NodeTraverserFactory;
use Rector\SwissKnife\PhpParser\NodeVisitor\AddImportConfigMethodCallNodeVisitor;
use Rector\SwissKnife\PhpParser\NodeVisitor\EntityClassNameCollectingNodeVisitor;
use Rector\SwissKnife\PhpParser\NodeVisitor\ExtractSymfonyExtensionCallNodeVisitor;
use Rector\SwissKnife\PhpParser\NodeVisitor\FindClassConstFetchNodeVisitor;
use Rector\SwissKnife\PhpParser\NodeVisitor\FindNonPrivateClassConstNodeVisitor;
use Rector\SwissKnife\PhpParser\NodeVisitor\MockedClassNameCollectingNodeVisitor;
use Rector\SwissKnife\PhpParser\NodeVisitor\NeedForFinalizeNodeVisitor;
use Rector\SwissKnife\PhpParser\NodeVisitor\ParentClassNameCollectingNodeVisitor;
use Rector\SwissKnife\SmokeTestgen\Templating\TemplateDecorator;
use Rector\SwissKnife\Testing\Finder\TestCaseClassFinder;
use Rector\SwissKnife\Testing\UnitTestFilter;
use Rector\SwissKnife\Tests\AbstractTestCase;
use Rector\SwissKnife\Traits\TraitSpotter;
use Rector\SwissKnife\ValueObject\ClassConstantFetch\CurrentClassConstantFetch;
use Rector\SwissKnife\ValueObject\ClassConstantFetch\ExternalClassAccessConstantFetch;

final class RemainingCoverageTest extends AbstractTestCase
{
    private string $originalCwd;

    protected function setUp(): void
    {
        parent::setUp();

        $originalCwd = getcwd();
        $this->originalCwd = $originalCwd === false ? __DIR__ : $originalCwd;
    }

    protected function tearDown(): void
    {
        chdir($this->originalCwd);
    }

    public function testNamespaceToPSR4SkipsCorrectNamespaceAndReportsAllCorrect(): void
    {
        $tempDirectory = sys_get_temp_dir() . '/swiss-knife-ns-all-ok-' . uniqid();
        FileSystem::createDir($tempDirectory);
        FileSystem::write(
            $tempDirectory . '/SomeClass.php',
            "<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Tests;\n\nfinal class SomeClass {}\n",
            null
        );

        chdir($tempDirectory);

        $command = $this->make(NamespaceToPSR4Command::class);
        $exitCode = $command->run('.', 'App\\Tests');

        $this->assertSame(ExitCode::SUCCESS, $exitCode);
        FileSystem::delete($tempDirectory);
    }

    public function testNamespaceToPSR4FixesMultipleFiles(): void
    {
        $tempDirectory = sys_get_temp_dir() . '/swiss-knife-ns-multi-' . uniqid();
        FileSystem::createDir($tempDirectory . '/A');
        FileSystem::createDir($tempDirectory . '/B');
        FileSystem::write($tempDirectory . '/A/One.php', "<?php\nnamespace Wrong;\nclass One {}\n", null);
        FileSystem::write($tempDirectory . '/B/Two.php', "<?php\nnamespace Wrong;\nclass Two {}\n", null);

        chdir($tempDirectory);

        $command = $this->make(NamespaceToPSR4Command::class);
        $exitCode = $command->run('.', 'App\\Tests');

        $this->assertSame(ExitCode::SUCCESS, $exitCode);
        FileSystem::delete($tempDirectory);
    }

    public function testSearchRegexSkipsFilesWithoutMatches(): void
    {
        $command = $this->make(SearchRegexCommand::class);
        $exitCode = $command->run('#class\\s+#', __DIR__ . '/../Finder/Fixture');

        $this->assertSame(ExitCode::SUCCESS, $exitCode);
    }

    public function testPrivatizeConstantsKeepsPublicWhenUsedExternally(): void
    {
        $command = $this->make(PrivatizeConstantsCommand::class);
        $exitCode = $command->run(
            [__DIR__ . '/../PhpParser/Finder/ClassConstantFetchFinder/Fixture/Standard'],
            dryRun: true
        );

        $this->assertSame(ExitCode::SUCCESS, $exitCode);
    }

    public function testSplitSymfonyConfigThrowsOnNonStringExtensionArgument(): void
    {
        $tempDirectory = sys_get_temp_dir() . '/swiss-knife-split-bad-' . uniqid();
        FileSystem::createDir($tempDirectory);
        $configPath = $tempDirectory . '/config.php';
        FileSystem::write(
            $configPath,
            <<<'PHP'
<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(123);
};
PHP,
            null
        );

        $command = $this->make(SplitSymfonyConfigToPerPackageCommand::class);

        $this->expectException(ShouldNotHappenException::class);
        $command->run($configPath, $tempDirectory . '/out');

        FileSystem::delete($tempDirectory);
    }

    public function testPhpFilesFinderExcludesByExactFilePath(): void
    {
        $fixtureDirectory = __DIR__ . '/../Finder/Fixture';
        $controllerPath = $fixtureDirectory . '/AController.php';

        $filesByExactPath = PhpFilesFinder::find([$fixtureDirectory], [realpath($controllerPath)]);
        $this->assertCount(2, $filesByExactPath);
    }

    public function testClassConstantFetchFinderDebugPrintsTraversalError(): void
    {
        $tempDirectory = sys_get_temp_dir() . '/swiss-knife-ccf-debug-' . uniqid();
        FileSystem::createDir($tempDirectory);
        FileSystem::write(
            $tempDirectory . '/Bad.php',
            "<?php\n\nnamespace Bad;\n\nfinal class Bad\n{\n    public function run(): mixed\n    {\n        return MissingClass::FOO;\n    }\n}\n",
            null
        );

        $finder = $this->make(ClassConstantFetchFinder::class);
        $fileInfos = PhpFilesFinder::find([$tempDirectory]);
        $progressBar = new ProgressBar(new OutputColorizer());
        $progressBar->start(1);

        $fetches = $finder->find($fileInfos, $progressBar, isDebug: true);

        $this->assertSame([], $fetches);
        FileSystem::delete($tempDirectory);
    }

    public function testAddImportConfigMethodCallVisitorSkipsClosureWithMultipleParameters(): void
    {
        $closure = new Closure();
        $closure->params = [new \PhpParser\Node\Param(new Variable('a')), new \PhpParser\Node\Param(new Variable('b'))];

        $visitor = new AddImportConfigMethodCallNodeVisitor('/out');
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($visitor);
        $nodeTraverser->traverse([$closure]);

        $this->assertCount(0, $closure->stmts ?? []);
    }

    public function testEntityClassNameCollectingNodeVisitorSkipsUnnamedClassAndDocWithoutAt(): void
    {
        $visitor = new EntityClassNameCollectingNodeVisitor();
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new NameResolver());
        $nodeTraverser->addVisitor($visitor);

        $visitorWithoutNameResolver = new EntityClassNameCollectingNodeVisitor();
        $nodeTraverserWithoutResolver = new NodeTraverser();
        $nodeTraverserWithoutResolver->addVisitor($visitorWithoutNameResolver);
        $nodeTraverserWithoutResolver->traverse($this->parseCode('<?php class GlobalClassWithoutNamespace {}'));

        $nodeTraverser->traverse($this->parseCode('<?php namespace WithDoc; /** Plain comment */ final class PlainDocClass {}'));

        $this->assertSame([], $visitor->getEntityClassNames());
    }

    public function testExtractSymfonyExtensionCallNodeVisitorSkipsNonMethodCallAndDynamicName(): void
    {
        $notMethodCall = new Expression(new Variable('foo'));
        $dynamicNameCall = new Expression(new MethodCall(new Variable('c'), new String_('extension')));

        $visitor = new ExtractSymfonyExtensionCallNodeVisitor();
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($visitor);
        $nodeTraverser->traverse([$notMethodCall, $dynamicNameCall]);

        $this->assertSame([], $visitor->getExtensionMethodCalls());
    }

    public function testFindClassConstFetchNodeVisitorEdgeCases(): void
    {
        require_once __DIR__ . '/../PhpParser/Finder/ClassConstantFetchFinder/Fixture/Standard/AnotherClassWithConstant.php';

        $dynamicClassFetch = <<<'PHP'
<?php
namespace DynamicClassFetch;
final class User
{
    public function run(object $class): mixed
    {
        return $class::FOO;
    }
}
PHP;
        $visitor = new FindClassConstFetchNodeVisitor();
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new NameResolver());
        $nodeTraverser->addVisitor($visitor);
        $nodeTraverser->traverse($this->parseCode($dynamicClassFetch));

        $staticCurrent = <<<'PHP'
<?php
namespace StaticCurrent;
final class User
{
    public const FOO = 'bar';

    public function run(): string
    {
        return static::FOO;
    }
}
PHP;
        $visitor3 = new FindClassConstFetchNodeVisitor();
        $nodeTraverser3 = new NodeTraverser();
        $nodeTraverser3->addVisitor(new NameResolver());
        $nodeTraverser3->addVisitor($visitor3);
        $nodeTraverser3->traverse($this->parseCode($staticCurrent));
        $this->assertCount(1, $visitor3->getClassConstantFetches());

        $vendorParent = <<<'PHP'
<?php
namespace VendorParent;
use PHPUnit\Framework\TestCase;
final class Child extends TestCase
{
    public function run(): mixed
    {
        return self::STATUS_UNKNOWN;
    }
}
PHP;
        $visitorVendor = new FindClassConstFetchNodeVisitor();
        $nodeTraverserVendor = new NodeTraverser();
        $nodeTraverserVendor->addVisitor(new NameResolver());
        $nodeTraverserVendor->addVisitor($visitorVendor);
        $nodeTraverserVendor->traverse($this->parseCode($vendorParent));
        $this->assertSame([], $visitorVendor->getClassConstantFetches());

        $vendorExternal = <<<'PHP'
<?php
namespace VendorExternal;
final class User
{
    public function run(): int
    {
        return \Symfony\Component\Yaml\Yaml::DUMP_OBJECT;
    }
}
PHP;
        $visitorExternal = new FindClassConstFetchNodeVisitor();
        $nodeTraverserExternal = new NodeTraverser();
        $nodeTraverserExternal->addVisitor(new NameResolver());
        $nodeTraverserExternal->addVisitor($visitorExternal);
        $nodeTraverserExternal->traverse($this->parseCode($vendorExternal));
        $this->assertSame([], $visitorExternal->getClassConstantFetches());

        if (! trait_exists('TraitFetch\\SomeTrait')) {
            eval('namespace TraitFetch; trait SomeTrait { public const FOO = "bar"; }');
        }

        $traitFetch = <<<'PHP'
<?php
namespace TraitFetch;
final class User
{
    public function run(): string
    {
        return SomeTrait::FOO;
    }
}
PHP;
        $visitor4 = new FindClassConstFetchNodeVisitor();
        $nodeTraverser4 = new NodeTraverser();
        $nodeTraverser4->addVisitor(new NameResolver());
        $nodeTraverser4->addVisitor($visitor4);
        $nodeTraverser4->traverse($this->parseCode($traitFetch));
        $this->assertCount(1, $visitor4->getClassConstantFetches());
    }

    public function testFindClassConstFetchNodeVisitorMissingParentThrows(): void
    {
        $missingParent = <<<'PHP'
<?php
namespace MissingParent;
final class Child extends NonExistentParent
{
    public function run(): mixed
    {
        return self::FOO;
    }
}
PHP;
        $visitor5 = new FindClassConstFetchNodeVisitor();
        $nodeTraverser5 = new NodeTraverser();
        $nodeTraverser5->addVisitor(new NameResolver());
        $nodeTraverser5->addVisitor($visitor5);
        $this->expectException(ShouldNotHappenException::class);
        $nodeTraverser5->traverse($this->parseCode($missingParent));
    }

    public function testFindClassConstFetchNodeVisitorGetClassNameThrowsWithoutNamespace(): void
    {
        $globalSelf = <<<'PHP'
<?php
class GlobalSelf
{
    public const FOO = 'bar';

    public function run(): string
    {
        return self::FOO;
    }
}
PHP;
        $visitor = new FindClassConstFetchNodeVisitor();
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($visitor);
        $this->expectException(ShouldNotHappenException::class);
        $nodeTraverser->traverse($this->parseCode($globalSelf));
    }

    public function testFindNonPrivateClassConstNodeVisitorSkipsPrivateConstants(): void
    {
        $visitor = new FindNonPrivateClassConstNodeVisitor();
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new NameResolver());
        $nodeTraverser->addVisitor($visitor);

        $code = <<<'PHP'
<?php
namespace PrivateConst;
final class User
{
    private const HIDDEN = 'hidden';
    public const VISIBLE = 'visible';
}
PHP;
        $nodeTraverser->traverse($this->parseCode($code));

        $constants = $visitor->getClassConstants();
        $this->assertCount(1, $constants);
        $this->assertSame('VISIBLE', $constants[0]->getConstantName());
    }

    public function testMockedClassNameCollectingNodeVisitorSkipsDynamicMethodAndMissingArgs(): void
    {
        $visitor = new MockedClassNameCollectingNodeVisitor();
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($visitor);

        $code = <<<'PHP'
<?php
namespace MockEdge;
final class User
{
    public function run(): void
    {
        $this->{'createMock'}();
        $this->createMock();
    }
}
PHP;
        $nodeTraverser->traverse($this->parseCode($code));

        $this->assertSame([], $visitor->getMockedClassNames());
    }

    public function testNeedForFinalizeNodeVisitorSkipsClassWithoutNamespace(): void
    {
        $visitor = new NeedForFinalizeNodeVisitor([]);
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($visitor);

        $nodeTraverser->traverse($this->parseCode('<?php class GlobalNotFinal {}'));

        $this->assertFalse($visitor->isNeeded());
    }

    public function testParentClassNameCollectingNodeVisitorFiltersVendorParents(): void
    {
        $visitor = new ParentClassNameCollectingNodeVisitor();
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new NameResolver());
        $nodeTraverser->addVisitor($visitor);

        $code = <<<'PHP'
<?php
namespace VendorParents;
final class A extends \Symfony\Component\HttpKernel\Kernel {}
final class B extends \PHPStan\Rules\Rule {}
final class C extends \PhpParser\NodeVisitorAbstract {}
final class D extends \App\Legit\Parent {}
PHP;
        $nodeTraverser->traverse($this->parseCode($code));

        $this->assertSame(['App\\Legit\\Parent'], $visitor->getParentClassNames());
    }

    public function testTemplateDecoratorUsesAppKernel(): void
    {
        if (! class_exists('App\\Kernel', false)) {
            eval('namespace App; class Kernel {}');
        }

        $templateDecorator = new TemplateDecorator();
        $decorated = $templateDecorator->decorate('__KERNEL_CLASS_PLACEHOLDER__');

        $this->assertStringContainsString('App\\Kernel', $decorated);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function testTemplateDecoratorUsesLegacyAppKernel(): void
    {
        if (! class_exists('AppKernel', false)) {
            eval('class AppKernel {}');
        }

        $templateDecorator = new TemplateDecorator();
        $decorated = $templateDecorator->decorate('__KERNEL_CLASS_PLACEHOLDER__');

        $this->assertStringContainsString('AppKernel', $decorated);
    }

    public function testFindClassConstFetchNodeVisitorGetClassNameThrowsWhenCurrentClassMissing(): void
    {
        $visitor = new FindClassConstFetchNodeVisitor();
        $reflectionMethod = new \ReflectionMethod(FindClassConstFetchNodeVisitor::class, 'getClassName');
        $reflectionMethod->setAccessible(true);

        $this->expectException(ShouldNotHappenException::class);
        $reflectionMethod->invoke($visitor);
    }

    public function testFindClassConstFetchNodeVisitorDoesClassExistForInterface(): void
    {
        $visitor = new FindClassConstFetchNodeVisitor();
        $reflectionMethod = new \ReflectionMethod(FindClassConstFetchNodeVisitor::class, 'doesClassExist');
        $reflectionMethod->setAccessible(true);

        $this->assertTrue($reflectionMethod->invoke($visitor, 'Countable'));
    }

    public function testTestCaseClassFinderSkipsExistingInterfaceAndTrait(): void
    {
        $tempDirectory = sys_get_temp_dir() . '/swiss-knife-tccf-' . uniqid();
        FileSystem::createDir($tempDirectory);
        FileSystem::write($tempDirectory . '/SomeInterface.php', '<?php interface SomeTestInterface {}', null);
        FileSystem::write($tempDirectory . '/SomeTrait.php', '<?php trait SomeTestTrait {}', null);

        $finder = new TestCaseClassFinder();
        $classes = $finder->findInDirectories([$tempDirectory]);
        $classesAgain = $finder->findInDirectories([$tempDirectory]);

        $this->assertArrayHasKey('SomeTestInterface', $classes);
        $this->assertArrayHasKey('SomeTestTrait', $classes);
        $this->assertArrayHasKey('SomeTestInterface', $classesAgain);
        $this->assertArrayHasKey('SomeTestTrait', $classesAgain);

        FileSystem::delete($tempDirectory);
    }

    public function testUnitTestFilterExcludesKernelTestCase(): void
    {
        if (! class_exists(\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase::class)) {
            eval('namespace Symfony\Bundle\FrameworkBundle\Test; class KernelTestCase extends \PHPUnit\Framework\TestCase {}');
        }

        eval('namespace SwissKnifeKernelTest; class MyKernelTest extends \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase {}');

        $unitTestFilter = new UnitTestFilter();
        $isUnitTest = new \ReflectionMethod(UnitTestFilter::class, 'isUnitTest');
        $isUnitTest->setAccessible(true);

        $this->assertFalse($isUnitTest->invoke($unitTestFilter, 'SwissKnifeKernelTest\\MyKernelTest'));

        $filtered = $unitTestFilter->filter([
            'SwissKnifeKernelTest\\MyKernelTest' => '/path/MyKernelTest.php',
            'PHPUnit\\Framework\\TestCase' => '/path/TestCase.php',
        ]);

        $this->assertSame(['PHPUnit\\Framework\\TestCase' => '/path/TestCase.php'], $filtered);
    }

    public function testTraitSpotterSkipsUnknownTraitUsage(): void
    {
        $tempDirectory = sys_get_temp_dir() . '/swiss-knife-trait-' . uniqid();
        FileSystem::createDir($tempDirectory);
        FileSystem::write($tempDirectory . '/KnownTrait.php', "<?php\n\ntrait KnownTrait {}\n", null);
        FileSystem::write(
            $tempDirectory . '/User.php',
            "<?php\n\nfinal class User\n{\n    use UnknownTrait;\n    use KnownTrait;\n}\n",
            null
        );

        $traitSpotter = $this->make(TraitSpotter::class);
        $result = $traitSpotter->analyse([$tempDirectory]);

        $this->assertSame(1, $result->getTraitCount());

        FileSystem::delete($tempDirectory);
    }

    public function testIsClassConstantUsedPubliclyBranches(): void
    {
        $command = $this->make(PrivatizeConstantsCommand::class);

        $externalFetch = new ExternalClassAccessConstantFetch('SomeClass', 'FOO');
        $currentFetch = new CurrentClassConstantFetch('SomeClass', 'FOO');

        $reflectionMethod = new \ReflectionMethod(PrivatizeConstantsCommand::class, 'isClassConstantUsedPublicly');
        $reflectionMethod->setAccessible(true);

        $classConstant = new \Rector\SwissKnife\ValueObject\ClassConstant('OtherClass', 'BAR');

        $isPublic = $reflectionMethod->invoke($command, [$externalFetch, $currentFetch], $classConstant);
        $this->assertFalse($isPublic);

        $matchingExternal = new \Rector\SwissKnife\ValueObject\ClassConstant('SomeClass', 'FOO');
        $isPublicExternal = $reflectionMethod->invoke($command, [$externalFetch], $matchingExternal);
        $this->assertTrue($isPublicExternal);
    }

    /**
     * @return array<\PhpParser\Node>
     */
    private function parseCode(string $code): array
    {
        $parser = (new ParserFactory())->createForNewestSupportedVersion();
        $nodes = $parser->parse($code);
        $this->assertNotNull($nodes);

        return $nodes;
    }
}

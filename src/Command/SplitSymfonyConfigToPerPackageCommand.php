<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use SwissKnife202605\Entropy\Console\Contract\CommandInterface;
use SwissKnife202605\Entropy\Console\Enum\ExitCode;
use SwissKnife202605\Nette\Utils\FileSystem;
use SwissKnife202605\PhpParser\Node\Expr\MethodCall;
use SwissKnife202605\PhpParser\Node\Scalar\String_;
use SwissKnife202605\PhpParser\Node\Stmt;
use SwissKnife202605\PhpParser\NodeTraverser;
use SwissKnife202605\PhpParser\NodeVisitor\NameResolver;
use SwissKnife202605\PhpParser\ParserFactory;
use SwissKnife202605\PhpParser\PrettyPrinter\Standard;
use Rector\SwissKnife\Exception\ShouldNotHappenException;
use Rector\SwissKnife\PhpParser\NodeFactory\SplitConfigClosureFactory;
use Rector\SwissKnife\PhpParser\NodeVisitor\AddImportConfigMethodCallNodeVisitor;
use Rector\SwissKnife\PhpParser\NodeVisitor\ExtractSymfonyExtensionCallNodeVisitor;
use SwissKnife202605\Symfony\Component\Console\Style\SymfonyStyle;
use SwissKnife202605\Webmozart\Assert\Assert;
final class SplitSymfonyConfigToPerPackageCommand implements CommandInterface
{
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @readonly
     * @var \Rector\SwissKnife\PhpParser\NodeFactory\SplitConfigClosureFactory
     */
    private $splitConfigClosureFactory;
    /**
     * @readonly
     * @var \PhpParser\PrettyPrinter\Standard
     */
    private $printerStandard;
    public function __construct(SymfonyStyle $symfonyStyle, SplitConfigClosureFactory $splitConfigClosureFactory)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->splitConfigClosureFactory = $splitConfigClosureFactory;
        $this->printerStandard = new Standard();
    }
    /**
     * @param string $configPath Path to the config file
     * @param string $outputDir Directory to save the split config files
     *
     * @return ExitCode::*
     */
    public function run(string $configPath, string $outputDir) : int
    {
        Assert::fileExists($configPath);
        Assert::notEmpty($outputDir);
        $stmts = $this->parseFilePathToStmts($configPath);
        $symfonyExtensionMethodCalls = $this->extractSymfonyExtensionMethodCalls($stmts);
        if ($symfonyExtensionMethodCalls === []) {
            $this->symfonyStyle->warning('No extension() method calls found');
            return ExitCode::SUCCESS;
        }
        foreach ($symfonyExtensionMethodCalls as $symfonyExtensionMethodCall) {
            $extensionNameString = $symfonyExtensionMethodCall->getArgs()[0]->value;
            if (!$extensionNameString instanceof String_) {
                throw new ShouldNotHappenException();
            }
            $configStmts = $this->splitConfigClosureFactory->createStmts($symfonyExtensionMethodCall);
            $splitConfigFileContents = $this->printerStandard->prettyPrintFile($configStmts);
            $splitConfigFilePath = $outputDir . '/' . $extensionNameString->value . '.php';
            FileSystem::write($splitConfigFilePath, $splitConfigFileContents, null);
        }
        // load packages from the output dir
        $addImportNodeTraverser = new NodeTraverser();
        $addImportNodeTraverser->addVisitor(new AddImportConfigMethodCallNodeVisitor($outputDir));
        $addImportNodeTraverser->traverse($stmts);
        // @todo print config back :)
        $cleanedConfigContents = $this->printerStandard->prettyPrintFile($stmts);
        FileSystem::write($configPath, $cleanedConfigContents, null);
        return 0;
    }
    public function getName() : string
    {
        return 'split-config-per-package';
    }
    public function getDescription() : string
    {
        return 'Split Symfony configs that contains many extension() calls to /packages directory with config per package';
    }
    /**
     * @return Stmt[]
     */
    private function parseFilePathToStmts(string $configPath) : array
    {
        $configContents = FileSystem::read($configPath);
        $parserFactory = new ParserFactory();
        $phpParser = $parserFactory->createForHostVersion();
        /** @var Stmt[] $stmts */
        $stmts = $phpParser->parse($configContents);
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new NameResolver());
        $nodeTraverser->traverse($stmts);
        return $stmts;
    }
    /**
     * @param Stmt[] $stmts
     * @return MethodCall[]
     */
    private function extractSymfonyExtensionMethodCalls(array $stmts) : array
    {
        $addImportNodeTraverser = new NodeTraverser();
        $extractSymfonyExtensionCallNodeVisitor = new ExtractSymfonyExtensionCallNodeVisitor();
        $addImportNodeTraverser->addVisitor($extractSymfonyExtensionCallNodeVisitor);
        $addImportNodeTraverser->traverse($stmts);
        return $extractSymfonyExtensionCallNodeVisitor->getExtensionMethodCalls();
    }
}

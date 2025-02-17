<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Nette\Utils\FileSystem;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Rector\SwissKnife\Exception\ShouldNotHappenException;
use Rector\SwissKnife\PhpParser\NodeFactory\SplitConfigClosureFactory;
use Rector\SwissKnife\PhpParser\NodeVisitor\AddImportConfigMethodCallNodeVisitor;
use Rector\SwissKnife\PhpParser\NodeVisitor\ExtractSymfonyExtensionCallNodeVisitor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Webmozart\Assert\Assert;

final class SplitSymfonyConfigToPerPackageCommand extends Command
{
    private readonly Standard $printerStandard;

    public function __construct(
        private readonly SymfonyStyle $symfonyStyle,
        private readonly SplitConfigClosureFactory $splitConfigClosureFactory,
    ) {
        $this->printerStandard = new Standard();

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('split-config-per-package');
        $this->setDescription('Split Symfony configs that contains many extension() calls to /packages directory with config per package');

        $this->addArgument('config-path', InputArgument::REQUIRED, 'Path to the config file');
        $this->addOption('output-dir', null, InputOption::VALUE_REQUIRED, 'Directory to save the split config files');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configPath = $input->getArgument('config-path');
        $outputDir = $input->getOption('output-dir');

        Assert::fileExists($configPath);
        Assert::notEmpty($outputDir);

        $stmts = $this->parseFilePathToStmts($configPath);

        $symfonyExtensionMethodCalls = $this->extractSymfonyExtensionMethodCalls($stmts);

        if ($symfonyExtensionMethodCalls === []) {
            $this->symfonyStyle->warning('No extension() method calls found');

            return self::SUCCESS;
        }

        foreach ($symfonyExtensionMethodCalls as $extensionMethodCall) {
            $extensionNameString = $extensionMethodCall->getArgs()[0]
                ->value;
            if (! $extensionNameString instanceof String_) {
                throw new ShouldNotHappenException();
            }

            $configStmts = $this->splitConfigClosureFactory->createStmts($extensionMethodCall);
            $splitConfigFileContents = $this->printerStandard->prettyPrintFile($configStmts);

            $splitConfigFilePath = $outputDir . '/' . $extensionNameString->value . '.php';

            FileSystem::write($splitConfigFilePath, $splitConfigFileContents);
        }

        // load packages from the output dir
        $addImportNodeTraverser = new NodeTraverser();
        $addImportNodeTraverser->addVisitor(new AddImportConfigMethodCallNodeVisitor($outputDir));
        $addImportNodeTraverser->traverse($stmts);

        // @todo print config back :)
        $cleanedConfigContents = $this->printerStandard->prettyPrintFile($stmts);
        FileSystem::write($configPath, $cleanedConfigContents);

        return 0;
    }

    /**
     * @return Stmt[]
     */
    private function parseFilePathToStmts(string $configPath): array
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
    private function extractSymfonyExtensionMethodCalls(array $stmts): array
    {
        $addImportNodeTraverser = new NodeTraverser();

        $extractSymfonyExtensionCallNodeVisitor = new ExtractSymfonyExtensionCallNodeVisitor();
        $addImportNodeTraverser->addVisitor($extractSymfonyExtensionCallNodeVisitor);
        $addImportNodeTraverser->traverse($stmts);

        return $extractSymfonyExtensionCallNodeVisitor->getExtensionMethodCalls();
    }
}

<?php

declare(strict_types=1);

namespace TomasVotruba\Lemonade\Command;

use PhpParser\ConstExprEvaluator;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeFinder;
use Rector\PhpParser\Node\Value\ValueResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\Lemonade\Finder\ConfigFilesFinder;
use TomasVotruba\Lemonade\PhpParser\CachedPhpParser;

final class AnalyseCommand extends Command
{
    public function __construct(
        private readonly NodeFinder $nodeFinder,
        private readonly SymfonyStyle $symfonyStyle,
        private readonly CachedPhpParser $cachedPhpParser,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('analyse');

        $this->setDescription('Find standalone service registrations, that are already covered by load');
        $this->addArgument('sources', InputArgument::REQUIRED, 'Path to your project');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string[] $sources */
        $sources = (array) $input->getArgument('sources');

        $serviceConfigFileInfos = ConfigFilesFinder::findServices($sources);

        // 1. find bare set() method calls
        // 2. find load() method calls

        /** @var MethodCall[] $bareSetMethodCalls */
        $bareSetMethodCalls = [];

        /** @var MethodCall[] $loadMethodCalls */
        $loadMethodCalls = [];

        foreach ($serviceConfigFileInfos as $serviceConfigFileInfo) {
            $stmts = $this->cachedPhpParser->parseFile($serviceConfigFileInfo->getRealPath());

            $currentBareSetMethodCalls = $this->findBareSetServiceMethodCalls($stmts);
            $bareSetMethodCalls = array_merge($bareSetMethodCalls, $currentBareSetMethodCalls);

            $currentLoadMethodCalls = $this->findLoadMethodCalls($stmts);
            $loadMethodCalls = array_merge($loadMethodCalls, $currentLoadMethodCalls);
        }

        $namespaceToPaths = [];

        foreach ($loadMethodCalls as $loadMethodCall) {
            $loadedNamespaceArg = $loadMethodCall->getArgs()[0];
            if (! $loadedNamespaceArg->value instanceof Node\Scalar\String_) {
                continue;
            }

            $namespaceToPaths[] = $loadedNamespaceArg->value->value;
        }

        sort($namespaceToPaths);

        $this->symfonyStyle->success(sprintf('Found %d bare "->set()" service registration calls', count($bareSetMethodCalls)));

        $this->symfonyStyle->success(sprintf('Found %d loaded namespaces', count($namespaceToPaths)));
        $this->symfonyStyle->listing($namespaceToPaths);

        $serviceClassNames = [];

        foreach ($bareSetMethodCalls as $bareSetMethodCall) {
            $serviceArg = $bareSetMethodCall->getArgs()[0];

            if ($serviceArg->value instanceof Node\Expr\ClassConstFetch) {
                $classConstFetch = $serviceArg->value;
                if ($classConstFetch->class instanceof Node\Name) {
                    $serviceClassNames[] = $classConstFetch->class->toString();
                }
            }
        }

        $foundDuplicateCount = 0;

        $alreadyRegistered = [];

        foreach ($serviceClassNames as $serviceClassName) {
            foreach ($namespaceToPaths as $namespaceToPath) {
                if (str_starts_with($serviceClassName, $namespaceToPath)) {
                    $alreadyRegistered[] = $serviceClassName;
                }
            }
        }

        if ($alreadyRegistered !== []) {
            $this->symfonyStyle->warning(sprintf('Found %d duplicate service registration', count($alreadyRegistered)));

            $this->symfonyStyle->listing($alreadyRegistered);
        }

        return self::SUCCESS;
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
    private function findLoadMethodCalls(array $stmts): array
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

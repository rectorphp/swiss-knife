<?php

declare(strict_types=1);

namespace TomasVotruba\Lemonade\Command;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeFinder;
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


        $bareSetMethodCalls = [];
        $loadMethodCalls = [];

        foreach ($serviceConfigFileInfos as $serviceConfigFileInfo) {
            $stmts = $this->cachedPhpParser->parseFile($serviceConfigFileInfo->getRealPath());

            $currentBareSetMethodCalls = $this->findBareSetServiceMethodCalls($stmts);
            $bareSetMethodCalls = array_merge($bareSetMethodCalls, $currentBareSetMethodCalls);

            $currentLoadMethodCalls = $this->findLoadMethodCalls($stmts);
            $loadMethodCalls = array_merge($loadMethodCalls, $currentLoadMethodCalls);
        }

        $this->symfonyStyle->note(sprintf('Found %d bare set() method calls', count($bareSetMethodCalls)));
        $this->symfonyStyle->note(sprintf('Found %d load() method calls', count($loadMethodCalls)));

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
            if (!$node instanceof Expression) {
                return false;
            }

            if (!$node->expr instanceof MethodCall) {
                return false;
            }

            $methodCall = $node->expr;
            if (! $methodCall->name instanceof Identifier) {
                return false;
            }

            if ($methodCall->name->name !== 'set') {
                return false;
            }

            if (!$methodCall->var instanceof Variable) {
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
     * @return Expression[]
     */
    private function findLoadMethodCalls(array $stmts): array
    {
        return $this->nodeFinder->find($stmts, function (Node $node) {
            if (!$node instanceof Expression) {
                return false;
            }

            if (!$node->expr instanceof MethodCall) {
                return false;
            }

            $methodCall = $node->expr;
            if (!$methodCall->name instanceof Identifier) {
                return false;
            }

            return $methodCall->name->name === 'load';
        });
    }
}

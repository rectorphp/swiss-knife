<?php

declare(strict_types=1);

namespace TomasVotruba\Lemonade\Command;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\Lemonade\Finder\ConfigFilesFinder;
use TomasVotruba\Lemonade\NodeFinder\ServiceMethodCallsFinder;

final class AnalyseCommand extends Command
{
    public function __construct(
        private readonly SymfonyStyle $symfonyStyle,
        private readonly ServiceMethodCallsFinder $serviceMethodCallsFinder,
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
        $bareSetMethodCalls = $this->serviceMethodCallsFinder->findSetMethodCalls($serviceConfigFileInfos);

        // 2. find load() method calls
        $loadMethodCalls = $this->serviceMethodCallsFinder->findLoadMethodCalls($serviceConfigFileInfos);

        $namespaceToPaths = $this->resolveLoadedNamespaces($loadMethodCalls);

        $this->symfonyStyle->success(
            sprintf('Found %d bare "->set()" service registration calls', count($bareSetMethodCalls))
        );

        $this->symfonyStyle->success(sprintf('Found %d loaded namespaces', count($namespaceToPaths)));
        $this->symfonyStyle->listing($namespaceToPaths);

        $serviceClassNames = $this->resolveServiceClassNames($bareSetMethodCalls);
        $alreadyRegistered = $this->filterAlreadyRegisteredServices($serviceClassNames, $namespaceToPaths);

        if ($alreadyRegistered !== []) {
            $this->symfonyStyle->warning(sprintf('Found %d duplicate service registration', count($alreadyRegistered)));

            $this->symfonyStyle->listing($alreadyRegistered);
        }

        return self::SUCCESS;
    }

    /**
     * @param MethodCall[] $loadMethodCalls
     * @return string[]
     */
    private function resolveLoadedNamespaces(array $loadMethodCalls): array
    {
        $namespaceToPaths = [];

        foreach ($loadMethodCalls as $loadMethodCall) {
            $loadedNamespaceArg = $loadMethodCall->getArgs()[0];
            if (! $loadedNamespaceArg->value instanceof String_) {
                continue;
            }

            $string = $loadedNamespaceArg->value;
            $namespaceToPaths[] = $string->value;
        }

        sort($namespaceToPaths);

        return $namespaceToPaths;
    }

    /**
     * @param MethodCall[] $bareSetMethodCalls
     * @return string[]
     */
    private function resolveServiceClassNames(array $bareSetMethodCalls): array
    {
        $serviceClassNames = [];

        foreach ($bareSetMethodCalls as $bareSetMethodCall) {
            $serviceArg = $bareSetMethodCall->getArgs()[0];

            if (! $serviceArg->value instanceof Node\Expr\ClassConstFetch) {
                continue;
            }

            $classConstFetch = $serviceArg->value;
            if (! $classConstFetch->class instanceof Node\Name) {
                continue;
            }

            $serviceClassNames[] = $classConstFetch->class->toString();
        }

        return $serviceClassNames;
    }

    /**
     * @param string[] $serviceClassNames
     * @param string[] $loadedNamespaces
     * @return string[]
     */
    private function filterAlreadyRegisteredServices(array $serviceClassNames, array $loadedNamespaces): array
    {
        $alreadyRegistered = [];

        foreach ($serviceClassNames as $serviceClassName) {
            foreach ($loadedNamespaces as $loadedNamespace) {
                if (str_starts_with($serviceClassName, $loadedNamespace)) {
                    $alreadyRegistered[] = $serviceClassName;
                }
            }
        }
        return $alreadyRegistered;
    }
}

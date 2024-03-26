<?php

declare(strict_types=1);

namespace TomasVotruba\Lemonade\Command;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\Lemonade\Finder\ConfigFilesFinder;
use TomasVotruba\Lemonade\NodeFinder\ServiceMethodCallsFinder;
use TomasVotruba\Lemonade\Resolver\BareRegisteredServicesResolver;

final class AnalyseCommand extends Command
{
    public function __construct(
        private readonly SymfonyStyle $symfonyStyle,
        private readonly ServiceMethodCallsFinder $serviceMethodCallsFinder,
        private readonly BareRegisteredServicesResolver $bareRegisteredServicesResolver,
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
        $projectDirectory = (string) $input->getArgument('sources');
        $projectDirectory = (string) realpath($projectDirectory);

        $serviceConfigFileInfos = ConfigFilesFinder::findServices($projectDirectory);

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

        $servicesToFiles = $this->bareRegisteredServicesResolver->resolveNameToConfigFile($bareSetMethodCalls);

        $alreadyRegisteredServicesToFile = $this->filterAlreadyRegisteredServicesToFile(
            $servicesToFiles,
            $namespaceToPaths
        );

        if ($alreadyRegisteredServicesToFile !== []) {
            $this->symfonyStyle->warning(
                sprintf('1. Found %d duplicate service registration', count($alreadyRegisteredServicesToFile))
            );

            foreach ($alreadyRegisteredServicesToFile as $service => $file) {
                $this->symfonyStyle->writeln(sprintf(' * %s in config file:', $service));
                $this->symfonyStyle->write($file);

                $this->symfonyStyle->newLine(2);
            }

            return self::FAILURE;
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
     * @param array<string, string> $servicesToFiles
     * @param string[] $loadedNamespaces
     * @return array<string, string>
     */
    private function filterAlreadyRegisteredServicesToFile(array $servicesToFiles, array $loadedNamespaces): array
    {
        $alreadyRegistered = [];

        foreach ($servicesToFiles as $service => $file) {
            foreach ($loadedNamespaces as $loadedNamespace) {
                if (str_starts_with($service, $loadedNamespace)) {
                    $alreadyRegistered[$service] = $file;
                }
            }
        }

        return $alreadyRegistered;
    }
}

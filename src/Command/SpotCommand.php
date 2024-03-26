<?php

declare(strict_types=1);

namespace TomasVotruba\Lemonade\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\Lemonade\Finder\ConfigFilesFinder;

final class SpotCommand extends Command
{
    public function __construct(
        private readonly SymfonyStyle $symfonyStyle,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('spot');

        $this->setDescription('Spot the config files in the project');
        $this->addArgument('sources', InputArgument::REQUIRED, 'Path to your project');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $projectDirectory = (string) $input->getArgument('sources');

        $serviceConfigFileInfos = ConfigFilesFinder::findServices($projectDirectory);

        foreach ($serviceConfigFileInfos as $serviceConfigFileInfo) {
            $this->symfonyStyle->writeln(' * ' . $serviceConfigFileInfo->getRelativePathname());
        }

        $this->symfonyStyle->success(sprintf('Found %d configs', count($serviceConfigFileInfos)));

        return self::SUCCESS;
    }
}

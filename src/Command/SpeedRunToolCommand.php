<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SpeedRunToolCommand extends Command
{
    public function __construct(
        private readonly SymfonyStyle $symfonyStyle,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('speed-run-tool');

        $this->setDescription('Test speed tool run, e.g. PHPStan or Rector, in various versions');

        $this->addOption('script-name', null, InputOption::VALUE_REQUIRED, 'Name of composer script to run');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $composerScriptName = (string) $input->getOption('script-name');

        dump($composerScriptName);

        new Process

        die;

        //@todo store to cache and wiat for next run

        dump($composerScriptName);
        die;

        return self::SUCCESS;
    }
}

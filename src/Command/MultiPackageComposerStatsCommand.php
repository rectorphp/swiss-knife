<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @deprecated This command is deprecated and out-sourced. Use "https://github.com/rectorphp/monitor" instead.
 */
final class MultiPackageComposerStatsCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('multi-package-composer-stats');

        $this->addArgument(
            'repositories',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'One or more repositories to compare package versions of'
        );

        $this->addOption(
            'is-source',
            null,
            InputOption::VALUE_NONE,
            'Provided repositories are main sources, analyze their dependencies instead'
        );

        $this->setDescription(
            'Compares package versions in multiple repositories, to easily sync multiple package upgrade'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $symfonyStyle->error(
            sprintf(
                'This command is deprecated and out-sourced. Use "%s" instead.',
                'https://github.com/rectorphp/monitor'
            )
        );

        return self::FAILURE;
    }
}

<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @deprecated This command is deprecated and out-sourced. Use "https://github.com/rectorphp/jack" instead.
 */
final class OutdatedBreakPointCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('outdated-breakpoint');

        $this->setDescription('Keep your major-version outdated packages low and check in CI');

        $this->addOption(
            'limit',
            null,
            InputOption::VALUE_REQUIRED,
            'Maximum number of outdated major version packages',
            10
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $symfonyStyle->error(
            sprintf(
                'This command is deprecated and out-sourced. Use "%s" instead.',
                'https://github.com/rectorphp/jack'
            )
        );

        return self::FAILURE;
    }
}

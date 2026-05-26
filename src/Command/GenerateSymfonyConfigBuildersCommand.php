<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Symfony\Component\Console\Style\SymfonyStyle;

final readonly class GenerateSymfonyConfigBuildersCommand implements CommandInterface
{
    public function __construct(
        private SymfonyStyle $symfonyStyle,
    ) {
    }

    public function getName(): string
    {
        return 'generate-symfony-config-builders';
    }

    public function getDescription(): string
    {
        return '[DEPRECATED] Symfony 5.3 config builders were deprecated in Symfony 7.4';
    }

    /**
     * @return ExitCode::*
     */
    public function run(): int
    {
        $this->symfonyStyle->error(
            'This command is deprecated. Symfony 5.3 config builders were deprecated in Symfony 7.4 in favor of the new PHP configuration API. See https://symfony.com/blog/new-in-symfony-7-4-better-php-configuration'
        );

        return ExitCode::ERROR;
    }
}

<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Output\OutputPrinter;

final readonly class GenerateSymfonyConfigBuildersCommand implements CommandInterface
{
    public function __construct(
        private OutputPrinter $outputPrinter,
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
        $this->outputPrinter->error(
            'This command is deprecated. Symfony 5.3 config builders were deprecated in Symfony 7.4 in favor of the new PHP configuration API. See https://symfony.com/blog/new-in-symfony-7-4-better-php-configuration'
        );

        return ExitCode::ERROR;
    }
}

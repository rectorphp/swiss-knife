<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use SwissKnife202606\Entropy\Console\Contract\CommandInterface;
use SwissKnife202606\Entropy\Console\Enum\ExitCode;
use SwissKnife202606\Symfony\Component\Console\Style\SymfonyStyle;
final class GenerateSymfonyConfigBuildersCommand implements CommandInterface
{
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }
    public function getName() : string
    {
        return 'generate-symfony-config-builders';
    }
    public function getDescription() : string
    {
        return '[DEPRECATED] Symfony 5.3 config builders were deprecated in Symfony 7.4';
    }
    /**
     * @return ExitCode::*
     */
    public function run() : int
    {
        $this->symfonyStyle->error('This command is deprecated. Symfony 5.3 config builders were deprecated in Symfony 7.4 in favor of the new PHP configuration API. See https://symfony.com/blog/new-in-symfony-7-4-better-php-configuration');
        return ExitCode::ERROR;
    }
}

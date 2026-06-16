<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use SwissKnife202606\Entropy\Console\Contract\CommandInterface;
use SwissKnife202606\Entropy\Console\Enum\ExitCode;
use SwissKnife202606\Entropy\Console\Output\OutputPrinter;
final class GenerateSymfonyConfigBuildersCommand implements CommandInterface
{
    /**
     * @readonly
     * @var \Entropy\Console\Output\OutputPrinter
     */
    private $outputPrinter;
    public function __construct(OutputPrinter $outputPrinter)
    {
        $this->outputPrinter = $outputPrinter;
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
        $this->outputPrinter->error('This command is deprecated. Symfony 5.3 config builders were deprecated in Symfony 7.4 in favor of the new PHP configuration API. See https://symfony.com/blog/new-in-symfony-7-4-better-php-configuration');
        return ExitCode::ERROR;
    }
}

<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Behastan\Command;

use SwissKnife202504\Symfony\Component\Console\Command\Command;
use SwissKnife202504\Symfony\Component\Console\Input\InputArgument;
use SwissKnife202504\Symfony\Component\Console\Input\InputInterface;
use SwissKnife202504\Symfony\Component\Console\Output\OutputInterface;
use SwissKnife202504\Symfony\Component\Console\Style\SymfonyStyle;
final class BehastanCommand extends Command
{
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('behastan');
        $this->setDescription('Checks Behat definitions in *Context.php files and feature files to spot unused ones');
        $this->addArgument('test-directory', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'One or more paths to check or *.Context.php and feature.yml files');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $this->symfonyStyle->warning('This rule was extracted to standalone tool and deprectaed here');
        $this->symfonyStyle->writeln('Use https://github.com/behastan/behastan/ instead');
        return self::FAILURE;
    }
}

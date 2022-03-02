<?php

declare (strict_types=1);
namespace EasyCI20220302\Symplify\PackageBuilder\Console\Style;

use EasyCI20220302\Symfony\Component\Console\Application;
use EasyCI20220302\Symfony\Component\Console\Input\ArgvInput;
use EasyCI20220302\Symfony\Component\Console\Output\ConsoleOutput;
use EasyCI20220302\Symfony\Component\Console\Output\OutputInterface;
use EasyCI20220302\Symfony\Component\Console\Style\SymfonyStyle;
use EasyCI20220302\Symplify\EasyTesting\PHPUnit\StaticPHPUnitEnvironment;
use EasyCI20220302\Symplify\PackageBuilder\Reflection\PrivatesCaller;
/**
 * @api
 */
final class SymfonyStyleFactory
{
    /**
     * @var \Symplify\PackageBuilder\Reflection\PrivatesCaller
     */
    private $privatesCaller;
    public function __construct()
    {
        $this->privatesCaller = new \EasyCI20220302\Symplify\PackageBuilder\Reflection\PrivatesCaller();
    }
    public function create() : \EasyCI20220302\Symfony\Component\Console\Style\SymfonyStyle
    {
        // to prevent missing argv indexes
        if (!isset($_SERVER['argv'])) {
            $_SERVER['argv'] = [];
        }
        $argvInput = new \EasyCI20220302\Symfony\Component\Console\Input\ArgvInput();
        $consoleOutput = new \EasyCI20220302\Symfony\Component\Console\Output\ConsoleOutput();
        // to configure all -v, -vv, -vvv options without memory-lock to Application run() arguments
        $this->privatesCaller->callPrivateMethod(new \EasyCI20220302\Symfony\Component\Console\Application(), 'configureIO', [$argvInput, $consoleOutput]);
        // --debug is called
        if ($argvInput->hasParameterOption('--debug')) {
            $consoleOutput->setVerbosity(\EasyCI20220302\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_DEBUG);
        }
        // disable output for tests
        if (\EasyCI20220302\Symplify\EasyTesting\PHPUnit\StaticPHPUnitEnvironment::isPHPUnitRun()) {
            $consoleOutput->setVerbosity(\EasyCI20220302\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_QUIET);
        }
        return new \EasyCI20220302\Symfony\Component\Console\Style\SymfonyStyle($argvInput, $consoleOutput);
    }
}

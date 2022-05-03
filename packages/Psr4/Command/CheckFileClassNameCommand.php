<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Psr4\Command;

use EasyCI20220503\Nette\Utils\Strings;
use EasyCI20220503\Symfony\Component\Console\Input\InputArgument;
use EasyCI20220503\Symfony\Component\Console\Input\InputInterface;
use EasyCI20220503\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Psr4\RobotLoader\PhpClassLoader;
use Symplify\EasyCI\Psr4\ValueObject\Option;
use EasyCI20220503\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use EasyCI20220503\Symplify\PackageBuilder\Console\Command\CommandNaming;
use EasyCI20220503\Symplify\SmartFileSystem\SmartFileInfo;
final class CheckFileClassNameCommand extends \EasyCI20220503\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand
{
    /**
     * @var \Symplify\EasyCI\Psr4\RobotLoader\PhpClassLoader
     */
    private $phpClassLoader;
    public function __construct(\Symplify\EasyCI\Psr4\RobotLoader\PhpClassLoader $phpClassLoader)
    {
        $this->phpClassLoader = $phpClassLoader;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName(\EasyCI20220503\Symplify\PackageBuilder\Console\Command\CommandNaming::classToName(self::class));
        $this->setDescription('Check if short file name is same as class name');
        $this->addArgument(\Symplify\EasyCI\Psr4\ValueObject\Option::SOURCES, \EasyCI20220503\Symfony\Component\Console\Input\InputArgument::REQUIRED | \EasyCI20220503\Symfony\Component\Console\Input\InputArgument::IS_ARRAY, 'Path to source');
    }
    protected function execute(\EasyCI20220503\Symfony\Component\Console\Input\InputInterface $input, \EasyCI20220503\Symfony\Component\Console\Output\OutputInterface $output) : int
    {
        /** @var string[] $sources */
        $sources = (array) $input->getArgument(\Symplify\EasyCI\Psr4\ValueObject\Option::SOURCES);
        $classesToFiles = $this->phpClassLoader->load($sources);
        $missMatchingClassNamesByFiles = [];
        foreach ($classesToFiles as $class => $file) {
            $fileBasename = \pathinfo($file, \PATHINFO_FILENAME);
            $shortClassName = \EasyCI20220503\Nette\Utils\Strings::after($class, '\\', -1);
            if ($shortClassName === $fileBasename) {
                continue;
            }
            $missMatchingClassNamesByFiles[$file] = $class;
        }
        if ($missMatchingClassNamesByFiles === []) {
            $this->symfonyStyle->success('All classes match their short file name');
            return self::SUCCESS;
        }
        foreach ($missMatchingClassNamesByFiles as $file => $class) {
            $fileInfo = new \EasyCI20220503\Symplify\SmartFileSystem\SmartFileInfo($file);
            $message = \sprintf('Check "%s" file to match class name "%s"', $fileInfo->getRelativeFilePathFromCwd(), $class);
            $this->symfonyStyle->warning($message);
        }
        return self::FAILURE;
    }
}

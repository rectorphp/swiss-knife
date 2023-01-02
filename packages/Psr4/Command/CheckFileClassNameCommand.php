<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Psr4\Command;

use EasyCI202301\Nette\Utils\Strings;
use EasyCI202301\Symfony\Component\Console\Input\InputArgument;
use EasyCI202301\Symfony\Component\Console\Input\InputInterface;
use EasyCI202301\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Psr4\RobotLoader\PhpClassLoader;
use Symplify\EasyCI\Psr4\ValueObject\Option;
use EasyCI202301\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use EasyCI202301\Symplify\SmartFileSystem\SmartFileInfo;
final class CheckFileClassNameCommand extends AbstractSymplifyCommand
{
    /**
     * @var \Symplify\EasyCI\Psr4\RobotLoader\PhpClassLoader
     */
    private $phpClassLoader;
    public function __construct(PhpClassLoader $phpClassLoader)
    {
        $this->phpClassLoader = $phpClassLoader;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('check-file-class-name');
        $this->setDescription('Check if short file name is same as class name');
        $this->addArgument(Option::SOURCES, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to source');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        /** @var string[] $sources */
        $sources = (array) $input->getArgument(Option::SOURCES);
        $classesToFiles = $this->phpClassLoader->load($sources);
        $message = \sprintf('Analyzing %d classes', \count($classesToFiles));
        $this->symfonyStyle->note($message);
        $missMatchingClassNamesByFiles = [];
        foreach ($classesToFiles as $class => $file) {
            $fileBasename = \pathinfo($file, \PATHINFO_FILENAME);
            $shortClassName = \strpos($class, '\\') !== \false ? Strings::after($class, '\\', -1) : $class;
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
            $fileInfo = new SmartFileInfo($file);
            $message = \sprintf('Check "%s" file to match class name "%s"', $fileInfo->getRelativeFilePathFromCwd(), $class);
            $this->symfonyStyle->warning($message);
        }
        return self::FAILURE;
    }
}

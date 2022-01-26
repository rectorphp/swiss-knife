<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Psr4\Command;

use EasyCI20220126\Symfony\Component\Console\Input\InputArgument;
use EasyCI20220126\Symfony\Component\Console\Input\InputInterface;
use EasyCI20220126\Symfony\Component\Console\Input\InputOption;
use EasyCI20220126\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Psr4\Configuration\Psr4SwitcherConfiguration;
use Symplify\EasyCI\Psr4\Json\JsonAutoloadPrinter;
use Symplify\EasyCI\Psr4\Psr4Filter;
use Symplify\EasyCI\Psr4\RobotLoader\PhpClassLoader;
use Symplify\EasyCI\Psr4\ValueObject\Option;
use Symplify\EasyCI\Psr4\ValueObject\Psr4NamespaceToPath;
use Symplify\EasyCI\Psr4\ValueObjectFactory\Psr4NamespaceToPathFactory;
use EasyCI20220126\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use EasyCI20220126\Symplify\PackageBuilder\Console\Command\CommandNaming;
final class GeneratePsr4ToPathsCommand extends \EasyCI20220126\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand
{
    /**
     * @var \Symplify\EasyCI\Psr4\Configuration\Psr4SwitcherConfiguration
     */
    private $psr4SwitcherConfiguration;
    /**
     * @var \Symplify\EasyCI\Psr4\RobotLoader\PhpClassLoader
     */
    private $phpClassLoader;
    /**
     * @var \Symplify\EasyCI\Psr4\ValueObjectFactory\Psr4NamespaceToPathFactory
     */
    private $psr4NamespaceToPathFactory;
    /**
     * @var \Symplify\EasyCI\Psr4\Psr4Filter
     */
    private $psr4Filter;
    /**
     * @var \Symplify\EasyCI\Psr4\Json\JsonAutoloadPrinter
     */
    private $jsonAutoloadPrinter;
    public function __construct(\Symplify\EasyCI\Psr4\Configuration\Psr4SwitcherConfiguration $psr4SwitcherConfiguration, \Symplify\EasyCI\Psr4\RobotLoader\PhpClassLoader $phpClassLoader, \Symplify\EasyCI\Psr4\ValueObjectFactory\Psr4NamespaceToPathFactory $psr4NamespaceToPathFactory, \Symplify\EasyCI\Psr4\Psr4Filter $psr4Filter, \Symplify\EasyCI\Psr4\Json\JsonAutoloadPrinter $jsonAutoloadPrinter)
    {
        $this->psr4SwitcherConfiguration = $psr4SwitcherConfiguration;
        $this->phpClassLoader = $phpClassLoader;
        $this->psr4NamespaceToPathFactory = $psr4NamespaceToPathFactory;
        $this->psr4Filter = $psr4Filter;
        $this->jsonAutoloadPrinter = $jsonAutoloadPrinter;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName(\EasyCI20220126\Symplify\PackageBuilder\Console\Command\CommandNaming::classToName(self::class));
        $this->setDescription('Check if application is PSR-4 ready');
        $this->addArgument(\Symplify\EasyCI\Psr4\ValueObject\Option::SOURCES, \EasyCI20220126\Symfony\Component\Console\Input\InputArgument::REQUIRED | \EasyCI20220126\Symfony\Component\Console\Input\InputArgument::IS_ARRAY, 'Path to source');
        $this->addOption(\Symplify\EasyCI\Psr4\ValueObject\Option::COMPOSER_JSON, null, \EasyCI20220126\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'Path to composer.json');
    }
    protected function execute(\EasyCI20220126\Symfony\Component\Console\Input\InputInterface $input, \EasyCI20220126\Symfony\Component\Console\Output\OutputInterface $output) : int
    {
        $this->psr4SwitcherConfiguration->loadFromInput($input);
        $classesToFiles = $this->phpClassLoader->load($this->psr4SwitcherConfiguration->getSource());
        $psr4NamespacesToPaths = [];
        $classesToFilesWithMissedCommonNamespace = [];
        foreach ($classesToFiles as $class => $file) {
            $psr4NamespaceToPath = $this->psr4NamespaceToPathFactory->createFromClassAndFile($class, $file);
            if (!$psr4NamespaceToPath instanceof \Symplify\EasyCI\Psr4\ValueObject\Psr4NamespaceToPath) {
                $classesToFilesWithMissedCommonNamespace[$class] = $file;
                continue;
            }
            $psr4NamespacesToPaths[] = $psr4NamespaceToPath;
        }
        $psr4NamespaceToPaths = $this->psr4Filter->filter($psr4NamespacesToPaths);
        $jsonAutoloadContent = $this->jsonAutoloadPrinter->createJsonAutoloadContent($psr4NamespaceToPaths);
        $this->symfonyStyle->writeln($jsonAutoloadContent);
        $this->symfonyStyle->success('Done');
        foreach ($classesToFilesWithMissedCommonNamespace as $class => $file) {
            $message = \sprintf('Class "%s" and file "%s" have no match in PSR-4 namespace', $class, $file);
            $this->symfonyStyle->warning($message);
        }
        return self::SUCCESS;
    }
}

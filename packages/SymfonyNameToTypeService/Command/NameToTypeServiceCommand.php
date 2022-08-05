<?php

declare (strict_types=1);
namespace Symplify\EasyCI\SymfonyNameToTypeService\Command;

use EasyCI202208\Symfony\Component\Console\Command\Command;
use EasyCI202208\Symfony\Component\Console\Input\InputArgument;
use EasyCI202208\Symfony\Component\Console\Input\InputInterface;
use EasyCI202208\Symfony\Component\Console\Input\InputOption;
use EasyCI202208\Symfony\Component\Console\Output\OutputInterface;
use EasyCI202208\Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCI\SymfonyNameToTypeService\AmbiguousServiceFilter;
use Symplify\EasyCI\SymfonyNameToTypeService\Finder\YamlConfigFinder;
use Symplify\EasyCI\SymfonyNameToTypeService\NameToTypeServiceReplacer;
use Symplify\EasyCI\SymfonyNameToTypeService\Option;
use Symplify\EasyCI\SymfonyNameToTypeService\XmlServiceMapFactory;
final class NameToTypeServiceCommand extends Command
{
    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @var \Symplify\EasyCI\SymfonyNameToTypeService\XmlServiceMapFactory
     */
    private $xmlServiceMapFactory;
    /**
     * @var \Symplify\EasyCI\SymfonyNameToTypeService\AmbiguousServiceFilter
     */
    private $ambiguousServiceFilter;
    /**
     * @var \Symplify\EasyCI\SymfonyNameToTypeService\Finder\YamlConfigFinder
     */
    private $yamlConfigFinder;
    /**
     * @var \Symplify\EasyCI\SymfonyNameToTypeService\NameToTypeServiceReplacer
     */
    private $nameToTypeServiceReplacer;
    public function __construct(SymfonyStyle $symfonyStyle, XmlServiceMapFactory $xmlServiceMapFactory, AmbiguousServiceFilter $ambiguousServiceFilter, YamlConfigFinder $yamlConfigFinder, NameToTypeServiceReplacer $nameToTypeServiceReplacer)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->xmlServiceMapFactory = $xmlServiceMapFactory;
        $this->ambiguousServiceFilter = $ambiguousServiceFilter;
        $this->yamlConfigFinder = $yamlConfigFinder;
        $this->nameToTypeServiceReplacer = $nameToTypeServiceReplacer;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('name-to-type-service');
        $this->setDescription('Replaces string names in Symfony 2.8 configs with typed-based names. This allows to get() by type from container');
        $this->addArgument(Option::SOURCES, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to directory/file with configs');
        $this->addOption(Option::XML_CONTAINER, null, InputOption::VALUE_REQUIRED, 'Path to dumped XML container');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        // use like: "php utils/name-to-type-service/bin/NameToTypeServiceCommand.php config"
        $configDirectory = $input->getArgument(Option::SOURCES);
        $xmlContainerFilePath = $input->getArgument(Option::XML_CONTAINER);
        // 1. get service map from xml dump
        $serviceTypesByName = $this->xmlServiceMapFactory->create($xmlContainerFilePath);
        $serviceTypesByName = $this->ambiguousServiceFilter->filter($serviceTypesByName);
        // 2. find yml configs
        $yamlFileInfos = $this->yamlConfigFinder->findInDirectory($configDirectory);
        // 3. replace names in config services by types
        $changedFilesCount = $this->nameToTypeServiceReplacer->replaceInFileInfos($yamlFileInfos, $serviceTypesByName);
        $successMessage = \sprintf('Updated %d config files', $changedFilesCount);
        $this->symfonyStyle->success($successMessage);
        return self::SUCCESS;
    }
}

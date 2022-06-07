<?php

declare (strict_types=1);
namespace EasyCI20220607\Symplify\EasyCI\StaticDetector\Command;

use EasyCI20220607\Symfony\Component\Console\Input\InputArgument;
use EasyCI20220607\Symfony\Component\Console\Input\InputInterface;
use EasyCI20220607\Symfony\Component\Console\Output\OutputInterface;
use EasyCI20220607\Symplify\EasyCI\StaticDetector\Collector\StaticNodeCollector;
use EasyCI20220607\Symplify\EasyCI\StaticDetector\Output\StaticReportReporter;
use EasyCI20220607\Symplify\EasyCI\StaticDetector\StaticScanner;
use EasyCI20220607\Symplify\EasyCI\ValueObject\Option;
use EasyCI20220607\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use EasyCI20220607\Symplify\PackageBuilder\Console\Command\CommandNaming;
final class DetectStaticCommand extends AbstractSymplifyCommand
{
    /**
     * @var \Symplify\EasyCI\StaticDetector\StaticScanner
     */
    private $staticScanner;
    /**
     * @var \Symplify\EasyCI\StaticDetector\Collector\StaticNodeCollector
     */
    private $staticNodeCollector;
    /**
     * @var \Symplify\EasyCI\StaticDetector\Output\StaticReportReporter
     */
    private $staticReportReporter;
    public function __construct(StaticScanner $staticScanner, StaticNodeCollector $staticNodeCollector, StaticReportReporter $staticReportReporter)
    {
        $this->staticScanner = $staticScanner;
        $this->staticNodeCollector = $staticNodeCollector;
        $this->staticReportReporter = $staticReportReporter;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->addArgument(Option::SOURCES, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'One or more directories to detect static in');
        $this->setDescription('Show what static method calls are called where');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $sources = (array) $input->getArgument(Option::SOURCES);
        $fileInfos = $this->smartFinder->find($sources, '*.php');
        $this->staticScanner->scanFileInfos($fileInfos);
        $this->symfonyStyle->title('Static Report');
        $staticReport = $this->staticNodeCollector->generateStaticReport();
        $this->staticReportReporter->reportStaticClassMethods($staticReport);
        $this->staticReportReporter->reportTotalNumbers($staticReport);
        return self::SUCCESS;
    }
}

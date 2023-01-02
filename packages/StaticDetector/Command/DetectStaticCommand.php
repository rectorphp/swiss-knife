<?php

declare (strict_types=1);
namespace Symplify\EasyCI\StaticDetector\Command;

use EasyCI202301\Symfony\Component\Console\Input\InputArgument;
use EasyCI202301\Symfony\Component\Console\Input\InputInterface;
use EasyCI202301\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\StaticDetector\Collector\StaticNodeCollector;
use Symplify\EasyCI\StaticDetector\Output\StaticReportReporter;
use Symplify\EasyCI\StaticDetector\StaticScanner;
use Symplify\EasyCI\ValueObject\Option;
use EasyCI202301\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
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
        $this->setName('detect-static');
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

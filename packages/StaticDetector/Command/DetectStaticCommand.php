<?php

declare (strict_types=1);
namespace Symplify\EasyCI\StaticDetector\Command;

use EasyCI20220307\Symfony\Component\Console\Input\InputArgument;
use EasyCI20220307\Symfony\Component\Console\Input\InputInterface;
use EasyCI20220307\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\StaticDetector\Collector\StaticNodeCollector;
use Symplify\EasyCI\StaticDetector\Output\StaticReportReporter;
use Symplify\EasyCI\StaticDetector\StaticScanner;
use Symplify\EasyCI\ValueObject\Option;
use EasyCI20220307\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use EasyCI20220307\Symplify\PackageBuilder\Console\Command\CommandNaming;
final class DetectStaticCommand extends \EasyCI20220307\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand
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
    public function __construct(\Symplify\EasyCI\StaticDetector\StaticScanner $staticScanner, \Symplify\EasyCI\StaticDetector\Collector\StaticNodeCollector $staticNodeCollector, \Symplify\EasyCI\StaticDetector\Output\StaticReportReporter $staticReportReporter)
    {
        $this->staticScanner = $staticScanner;
        $this->staticNodeCollector = $staticNodeCollector;
        $this->staticReportReporter = $staticReportReporter;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName(\EasyCI20220307\Symplify\PackageBuilder\Console\Command\CommandNaming::classToName(self::class));
        $this->addArgument(\Symplify\EasyCI\ValueObject\Option::SOURCES, \EasyCI20220307\Symfony\Component\Console\Input\InputArgument::REQUIRED | \EasyCI20220307\Symfony\Component\Console\Input\InputArgument::IS_ARRAY, 'One or more directories to detect static in');
        $this->setDescription('Show what static method calls are called where');
    }
    protected function execute(\EasyCI20220307\Symfony\Component\Console\Input\InputInterface $input, \EasyCI20220307\Symfony\Component\Console\Output\OutputInterface $output) : int
    {
        $sources = (array) $input->getArgument(\Symplify\EasyCI\ValueObject\Option::SOURCES);
        $fileInfos = $this->smartFinder->find($sources, '*.php');
        $this->staticScanner->scanFileInfos($fileInfos);
        $this->symfonyStyle->title('Static Report');
        $staticReport = $this->staticNodeCollector->generateStaticReport();
        $this->staticReportReporter->reportStaticClassMethods($staticReport);
        $this->staticReportReporter->reportTotalNumbers($staticReport);
        return self::SUCCESS;
    }
}

<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Neon\Command;

use EasyCI20220211\Symfony\Component\Console\Input\InputArgument;
use EasyCI20220211\Symfony\Component\Console\Input\InputInterface;
use EasyCI20220211\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Console\Output\FileErrorsReporter;
use Symplify\EasyCI\Neon\Application\NeonFilesProcessor;
use Symplify\EasyCI\ValueObject\Option;
use EasyCI20220211\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use EasyCI20220211\Symplify\PackageBuilder\Console\Command\CommandNaming;
final class CheckNeonCommand extends \EasyCI20220211\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand
{
    /**
     * @var \Symplify\EasyCI\Neon\Application\NeonFilesProcessor
     */
    private $neonFilesProcessor;
    /**
     * @var \Symplify\EasyCI\Console\Output\FileErrorsReporter
     */
    private $fileErrorsReporter;
    public function __construct(\Symplify\EasyCI\Neon\Application\NeonFilesProcessor $neonFilesProcessor, \Symplify\EasyCI\Console\Output\FileErrorsReporter $fileErrorsReporter)
    {
        $this->neonFilesProcessor = $neonFilesProcessor;
        $this->fileErrorsReporter = $fileErrorsReporter;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName(\EasyCI20220211\Symplify\PackageBuilder\Console\Command\CommandNaming::classToName(self::class));
        $this->addArgument(\Symplify\EasyCI\ValueObject\Option::SOURCES, \EasyCI20220211\Symfony\Component\Console\Input\InputArgument::REQUIRED | \EasyCI20220211\Symfony\Component\Console\Input\InputArgument::IS_ARRAY, 'One or more paths with templates');
        $this->setDescription('Analyze NEON files for complex syntax');
    }
    protected function execute(\EasyCI20220211\Symfony\Component\Console\Input\InputInterface $input, \EasyCI20220211\Symfony\Component\Console\Output\OutputInterface $output) : int
    {
        $sources = (array) $input->getArgument(\Symplify\EasyCI\ValueObject\Option::SOURCES);
        $neonFileInfos = $this->smartFinder->find($sources, '*.neon');
        $message = \sprintf('Analysing %d *.neon files', \count($neonFileInfos));
        $this->symfonyStyle->note($message);
        $fileErrors = $this->neonFilesProcessor->processFileInfos($neonFileInfos);
        return $this->fileErrorsReporter->report($fileErrors);
    }
}

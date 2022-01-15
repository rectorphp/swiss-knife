<?php

declare (strict_types=1);
namespace EasyCI20220115\Symplify\EasyCI\Command;

use EasyCI20220115\Symfony\Component\Console\Input\InputArgument;
use EasyCI20220115\Symfony\Component\Console\Input\InputInterface;
use EasyCI20220115\Symfony\Component\Console\Output\OutputInterface;
use EasyCI20220115\Symplify\EasyCI\Console\Output\FileErrorsReporter;
use EasyCI20220115\Symplify\EasyCI\Latte\LatteTemplateProcessor;
use EasyCI20220115\Symplify\EasyCI\ValueObject\Option;
use EasyCI20220115\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use EasyCI20220115\Symplify\PackageBuilder\Console\Command\CommandNaming;
final class CheckLatteTemplateCommand extends \EasyCI20220115\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand
{
    /**
     * @var \Symplify\EasyCI\Latte\LatteTemplateProcessor
     */
    private $latteTemplateProcessor;
    /**
     * @var \Symplify\EasyCI\Console\Output\FileErrorsReporter
     */
    private $fileErrorsReporter;
    public function __construct(\EasyCI20220115\Symplify\EasyCI\Latte\LatteTemplateProcessor $latteTemplateProcessor, \EasyCI20220115\Symplify\EasyCI\Console\Output\FileErrorsReporter $fileErrorsReporter)
    {
        $this->latteTemplateProcessor = $latteTemplateProcessor;
        $this->fileErrorsReporter = $fileErrorsReporter;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName(\EasyCI20220115\Symplify\PackageBuilder\Console\Command\CommandNaming::classToName(self::class));
        $this->addArgument(\EasyCI20220115\Symplify\EasyCI\ValueObject\Option::SOURCES, \EasyCI20220115\Symfony\Component\Console\Input\InputArgument::REQUIRED | \EasyCI20220115\Symfony\Component\Console\Input\InputArgument::IS_ARRAY, 'One or more paths with templates');
        $this->setDescription('Analyze missing classes, constant and static calls in Latte templates');
    }
    protected function execute(\EasyCI20220115\Symfony\Component\Console\Input\InputInterface $input, \EasyCI20220115\Symfony\Component\Console\Output\OutputInterface $output) : int
    {
        $sources = (array) $input->getArgument(\EasyCI20220115\Symplify\EasyCI\ValueObject\Option::SOURCES);
        $latteFileInfos = $this->smartFinder->find($sources, '*.latte');
        $message = \sprintf('Analysing %d *.latte files', \count($latteFileInfos));
        $this->symfonyStyle->note($message);
        $fileErrors = $this->latteTemplateProcessor->analyzeFileInfos($latteFileInfos);
        return $this->fileErrorsReporter->report($fileErrors);
    }
}

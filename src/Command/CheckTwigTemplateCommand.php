<?php

declare (strict_types=1);
namespace EasyCI20220607\Symplify\EasyCI\Command;

use EasyCI20220607\Symfony\Component\Console\Input\InputArgument;
use EasyCI20220607\Symfony\Component\Console\Input\InputInterface;
use EasyCI20220607\Symfony\Component\Console\Output\OutputInterface;
use EasyCI20220607\Symplify\EasyCI\Console\Output\FileErrorsReporter;
use EasyCI20220607\Symplify\EasyCI\Twig\TwigTemplateProcessor;
use EasyCI20220607\Symplify\EasyCI\ValueObject\Option;
use EasyCI20220607\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use EasyCI20220607\Symplify\PackageBuilder\Console\Command\CommandNaming;
final class CheckTwigTemplateCommand extends AbstractSymplifyCommand
{
    /**
     * @var \Symplify\EasyCI\Twig\TwigTemplateProcessor
     */
    private $twigTemplateProcessor;
    /**
     * @var \Symplify\EasyCI\Console\Output\FileErrorsReporter
     */
    private $fileErrorsReporter;
    public function __construct(TwigTemplateProcessor $twigTemplateProcessor, FileErrorsReporter $fileErrorsReporter)
    {
        $this->twigTemplateProcessor = $twigTemplateProcessor;
        $this->fileErrorsReporter = $fileErrorsReporter;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->addArgument(Option::SOURCES, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'One or more paths with templates');
        $this->setDescription('Analyze missing classes, constant and static calls in Latte templates');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $sources = (array) $input->getArgument(Option::SOURCES);
        $twigFileInfos = $this->smartFinder->find($sources, '*.twig');
        $message = \sprintf('Analysing %d *.twig files', \count($twigFileInfos));
        $this->symfonyStyle->note($message);
        $fileErrors = $this->twigTemplateProcessor->analyzeFileInfos($twigFileInfos);
        return $this->fileErrorsReporter->report($fileErrors);
    }
}

<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Command;

use EasyCI20220121\Symfony\Component\Console\Input\InputArgument;
use EasyCI20220121\Symfony\Component\Console\Input\InputInterface;
use EasyCI20220121\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Console\Output\FileErrorsReporter;
use Symplify\EasyCI\Twig\TwigTemplateProcessor;
use Symplify\EasyCI\ValueObject\Option;
use EasyCI20220121\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use EasyCI20220121\Symplify\PackageBuilder\Console\Command\CommandNaming;
final class CheckTwigTemplateCommand extends \EasyCI20220121\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand
{
    /**
     * @var \Symplify\EasyCI\Twig\TwigTemplateProcessor
     */
    private $twigTemplateProcessor;
    /**
     * @var \Symplify\EasyCI\Console\Output\FileErrorsReporter
     */
    private $fileErrorsReporter;
    public function __construct(\Symplify\EasyCI\Twig\TwigTemplateProcessor $twigTemplateProcessor, \Symplify\EasyCI\Console\Output\FileErrorsReporter $fileErrorsReporter)
    {
        $this->twigTemplateProcessor = $twigTemplateProcessor;
        $this->fileErrorsReporter = $fileErrorsReporter;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName(\EasyCI20220121\Symplify\PackageBuilder\Console\Command\CommandNaming::classToName(self::class));
        $this->addArgument(\Symplify\EasyCI\ValueObject\Option::SOURCES, \EasyCI20220121\Symfony\Component\Console\Input\InputArgument::REQUIRED | \EasyCI20220121\Symfony\Component\Console\Input\InputArgument::IS_ARRAY, 'One or more paths with templates');
        $this->setDescription('Analyze missing classes, constant and static calls in Latte templates');
    }
    protected function execute(\EasyCI20220121\Symfony\Component\Console\Input\InputInterface $input, \EasyCI20220121\Symfony\Component\Console\Output\OutputInterface $output) : int
    {
        $sources = (array) $input->getArgument(\Symplify\EasyCI\ValueObject\Option::SOURCES);
        $twigFileInfos = $this->smartFinder->find($sources, '*.twig');
        $message = \sprintf('Analysing %d *.twig files', \count($twigFileInfos));
        $this->symfonyStyle->note($message);
        $fileErrors = $this->twigTemplateProcessor->analyzeFileInfos($twigFileInfos);
        return $this->fileErrorsReporter->report($fileErrors);
    }
}

<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Command;

use EasyCI20220607\Symfony\Component\Console\Input\InputArgument;
use EasyCI20220607\Symfony\Component\Console\Input\InputInterface;
use EasyCI20220607\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Console\Output\FileErrorsReporter;
use Symplify\EasyCI\Latte\LatteTemplateProcessor;
use Symplify\EasyCI\ValueObject\Option;
use EasyCI20220607\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use EasyCI20220607\Symplify\PackageBuilder\Console\Command\CommandNaming;
final class CheckLatteTemplateCommand extends AbstractSymplifyCommand
{
    /**
     * @var \Symplify\EasyCI\Latte\LatteTemplateProcessor
     */
    private $latteTemplateProcessor;
    /**
     * @var \Symplify\EasyCI\Console\Output\FileErrorsReporter
     */
    private $fileErrorsReporter;
    public function __construct(LatteTemplateProcessor $latteTemplateProcessor, FileErrorsReporter $fileErrorsReporter)
    {
        $this->latteTemplateProcessor = $latteTemplateProcessor;
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
        $latteFileInfos = $this->smartFinder->find($sources, '*.latte');
        $message = \sprintf('Analysing %d *.latte files', \count($latteFileInfos));
        $this->symfonyStyle->note($message);
        $fileErrors = $this->latteTemplateProcessor->analyzeFileInfos($latteFileInfos);
        return $this->fileErrorsReporter->report($fileErrors);
    }
}

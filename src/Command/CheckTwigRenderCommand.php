<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Command;

use EasyCI20220606\Symfony\Component\Console\Input\InputArgument;
use EasyCI20220606\Symfony\Component\Console\Input\InputInterface;
use EasyCI20220606\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Console\Output\MissingTwigTemplatePathReporter;
use Symplify\EasyCI\Template\RenderMethodTemplateExtractor;
use Symplify\EasyCI\Template\TemplatePathsResolver;
use Symplify\EasyCI\Twig\TwigAnalyzer;
use Symplify\EasyCI\ValueObject\Option;
use EasyCI20220606\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use EasyCI20220606\Symplify\PackageBuilder\Console\Command\CommandNaming;
final class CheckTwigRenderCommand extends \EasyCI20220606\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand
{
    /**
     * @var \Symplify\EasyCI\Template\TemplatePathsResolver
     */
    private $templatePathsResolver;
    /**
     * @var \Symplify\EasyCI\Template\RenderMethodTemplateExtractor
     */
    private $renderMethodTemplateExtractor;
    /**
     * @var \Symplify\EasyCI\Twig\TwigAnalyzer
     */
    private $twigAnalyzer;
    /**
     * @var \Symplify\EasyCI\Console\Output\MissingTwigTemplatePathReporter
     */
    private $missingTwigTemplatePathReporter;
    public function __construct(\Symplify\EasyCI\Template\TemplatePathsResolver $templatePathsResolver, \Symplify\EasyCI\Template\RenderMethodTemplateExtractor $renderMethodTemplateExtractor, \Symplify\EasyCI\Twig\TwigAnalyzer $twigAnalyzer, \Symplify\EasyCI\Console\Output\MissingTwigTemplatePathReporter $missingTwigTemplatePathReporter)
    {
        $this->templatePathsResolver = $templatePathsResolver;
        $this->renderMethodTemplateExtractor = $renderMethodTemplateExtractor;
        $this->twigAnalyzer = $twigAnalyzer;
        $this->missingTwigTemplatePathReporter = $missingTwigTemplatePathReporter;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName(\EasyCI20220606\Symplify\PackageBuilder\Console\Command\CommandNaming::classToName(self::class));
        $this->setDescription('Validate template paths in $this->render(...)');
        $this->addArgument(\Symplify\EasyCI\ValueObject\Option::SOURCES, \EasyCI20220606\Symfony\Component\Console\Input\InputArgument::REQUIRED | \EasyCI20220606\Symfony\Component\Console\Input\InputArgument::IS_ARRAY, 'Path to project directories');
    }
    protected function execute(\EasyCI20220606\Symfony\Component\Console\Input\InputInterface $input, \EasyCI20220606\Symfony\Component\Console\Output\OutputInterface $output) : int
    {
        /** @var string[] $sources */
        $sources = (array) $input->getArgument(\Symplify\EasyCI\ValueObject\Option::SOURCES);
        $this->symfonyStyle->title('Analysing controllers and templates');
        $stats = [];
        $controllerFileInfos = $this->smartFinder->find($sources, '#Controller\\.php$#');
        $stats[] = \sprintf('%d controllers', \count($controllerFileInfos));
        $allowedTemplatePaths = $this->templatePathsResolver->resolveFromDirectories($sources);
        $stats[] = \sprintf('%d twig paths', \count($allowedTemplatePaths));
        $usedTemplatePaths = $this->renderMethodTemplateExtractor->extractFromFileInfos($controllerFileInfos);
        $stats[] = \sprintf('%d unique used templates in "$this->render()" method', \count($usedTemplatePaths));
        $this->symfonyStyle->listing($stats);
        $this->symfonyStyle->newLine(2);
        $errorMessages = $this->twigAnalyzer->analyzeFileInfos($controllerFileInfos, $allowedTemplatePaths);
        return $this->missingTwigTemplatePathReporter->report($errorMessages);
    }
}

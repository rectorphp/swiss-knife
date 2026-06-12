<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Command;

use SwissKnife202606\Entropy\Console\Contract\CommandInterface;
use SwissKnife202606\Entropy\Console\Enum\ExitCode;
use SwissKnife202606\Entropy\Console\Output\OutputPrinter;
use SwissKnife202606\Nette\Utils\FileSystem;
use SwissKnife202606\Nette\Utils\Json;
use Rector\SwissKnife\SmokeTestgen\FileSystem\TestsDirectoryResolver;
use Rector\SwissKnife\SmokeTestgen\Templating\TemplateDecorator;
use Rector\SwissKnife\SmokeTestgen\TestTemplateResolver;
use Rector\SwissKnife\SmokeTestgen\Utils\TestPathResolver;
use SwissKnife202606\Webmozart\Assert\Assert;
final class GenerateSymfonySmokeTestsCommand implements CommandInterface
{
    /**
     * @readonly
     * @var \Rector\SwissKnife\SmokeTestgen\FileSystem\TestsDirectoryResolver
     */
    private $testsDirectoryResolver;
    /**
     * @readonly
     * @var \Rector\SwissKnife\SmokeTestgen\TestTemplateResolver
     */
    private $testTemplateResolver;
    /**
     * @readonly
     * @var \Rector\SwissKnife\SmokeTestgen\Templating\TemplateDecorator
     */
    private $templateDecorator;
    /**
     * @readonly
     * @var \Entropy\Console\Output\OutputPrinter
     */
    private $outputPrinter;
    public function __construct(TestsDirectoryResolver $testsDirectoryResolver, TestTemplateResolver $testTemplateResolver, TemplateDecorator $templateDecorator, OutputPrinter $outputPrinter)
    {
        $this->testsDirectoryResolver = $testsDirectoryResolver;
        $this->testTemplateResolver = $testTemplateResolver;
        $this->templateDecorator = $templateDecorator;
        $this->outputPrinter = $outputPrinter;
    }
    public function getName() : string
    {
        return 'generate-symfony-smoke-tests';
    }
    public function getDescription() : string
    {
        return 'Generate Symfony smoke tests in "tests/Unit/Smoke" namespace';
    }
    /**
     * @return ExitCode::*
     */
    public function run() : int
    {
        $this->outputPrinter->writeln('<fg=green>Resolving directory for smoke tests</>');
        $smokeTestsDirectory = $this->testsDirectoryResolver->resolveSmokeUnitTestDirectory(\getcwd());
        $this->outputPrinter->writeln(' * ' . $smokeTestsDirectory);
        $requirePackages = $this->resolveProjectRequiredPackageNames(\getcwd());
        $testByPackageSubscribers = $this->testTemplateResolver->matchProjectPackages($requirePackages);
        if ($testByPackageSubscribers === []) {
            $this->outputPrinter->warning('No test templates found for the required packages. Make sure you project uses Composer to manage version and has Symfony/Doctrine packages listed in "require" section');
            return ExitCode::ERROR;
        }
        $this->outputPrinter->newline();
        $this->outputPrinter->writeln(\sprintf('Found <fg=yellow>%d smoke test%s</> that might come handy', \count($testByPackageSubscribers), \count($testByPackageSubscribers) > 1 ? 's' : ''));
        $generatedTestCount = 0;
        foreach ($testByPackageSubscribers as $test) {
            $projectTestFilePath = TestPathResolver::resolve($test, $smokeTestsDirectory);
            if (\file_exists($projectTestFilePath)) {
                $this->outputPrinter->writeln(\sprintf('File <fg=green>%s</> already exists, skipping', $projectTestFilePath));
                continue;
            }
            $templateContents = FileSystem::read($test->getTemplateFilePath());
            $templateContents = $this->templateDecorator->decorate($templateContents);
            FileSystem::write($projectTestFilePath, $templateContents);
            $this->outputPrinter->writeln(\sprintf('Generated new test file %s', $projectTestFilePath));
            ++$generatedTestCount;
        }
        if ($generatedTestCount === 0) {
            $this->outputPrinter->success('No new test files were generated. All required tests already exist.');
            return ExitCode::SUCCESS;
        }
        // make sure the abstract test case is always present
        $projectTestCaseFilePath = $smokeTestsDirectory . '/AbstractContainerTestCase.php';
        if (!\file_exists($projectTestCaseFilePath)) {
            $templateContents = FileSystem::read(__DIR__ . '/../../templates/SmokeTests/Symfony/AbstractContainerTestCase.php');
            $templateContents = $this->templateDecorator->decorate($templateContents);
            FileSystem::write($projectTestCaseFilePath, $templateContents);
        }
        $this->outputPrinter->success(\sprintf('Generated %d new test file%s in "%s"', $generatedTestCount, $generatedTestCount > 1 ? 's' : '', $smokeTestsDirectory));
        $this->outputPrinter->newline();
        return ExitCode::SUCCESS;
    }
    /**
     * @return string[]
     */
    private function resolveProjectRequiredPackageNames(string $projectDirectory) : array
    {
        // load composer.json and read package names from "require"
        $composerJsonFilePath = $projectDirectory . '/composer.json';
        Assert::fileExists($composerJsonFilePath);
        $composerJson = Json::decode(FileSystem::read($composerJsonFilePath), \true);
        $requirePackagesToVersions = $composerJson['require'] ?? [];
        $packageNames = \array_keys($requirePackagesToVersions);
        Assert::allString($packageNames);
        return $packageNames;
    }
}

<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Output\OutputPrinter;
use Entropy\Validation\Assert;
use Rector\SwissKnife\Lines\FeatureCounter\FeatureCounterAnalyzer;
use Rector\SwissKnife\Lines\FeatureCounter\ResultPrinter;
use Rector\SwissKnife\Lines\Finder\ProjectFilesFinder;
use Rector\SwissKnife\Lines\OutputFormatter\JsonOutputFormatter;

final class FeaturesCommand implements CommandInterface
{
    /**
     * @readonly
     */
    private OutputPrinter $outputPrinter;

    /**
     * @readonly
     */
    private FeatureCounterAnalyzer $featureCounterAnalyzer;

    /**
     * @readonly
     */
    private ResultPrinter $resultPrinter;

    /**
     * @readonly
     */
    private JsonOutputFormatter $jsonOutputFormatter;

    public function __construct(OutputPrinter $outputPrinter, FeatureCounterAnalyzer $featureCounterAnalyzer, ResultPrinter $resultPrinter, JsonOutputFormatter $jsonOutputFormatter)
    {
        $this->outputPrinter = $outputPrinter;
        $this->featureCounterAnalyzer = $featureCounterAnalyzer;
        $this->resultPrinter = $resultPrinter;
        $this->jsonOutputFormatter = $jsonOutputFormatter;
    }

    /**
     * @param string[] $projectDirectories Project directories to analyze
     * @param bool $json Output in JSON format
     *
     * @return ExitCode::*
     */
    public function run(array $projectDirectories, bool $json = false): int
    {
        $allFileInfos = [];
        foreach ($projectDirectories as $projectDirectory) {
            Assert::directory($projectDirectory, sprintf('The directory "%s" does not exist.', $projectDirectory));

            $fileInfos = ProjectFilesFinder::find($projectDirectory);
            $allFileInfos = array_merge($allFileInfos, $fileInfos);
        }

        $featureCollector = $this->featureCounterAnalyzer->analyze($allFileInfos);

        if ($json) {
            $this->jsonOutputFormatter->printFeatures($featureCollector);
            return ExitCode::SUCCESS;
        }

        $this->outputPrinter->title('PHP features');
        $this->resultPrinter->print($featureCollector);

        return ExitCode::SUCCESS;
    }

    public function getName(): string
    {
        return 'features';
    }

    public function getDescription(): string
    {
        return 'Count used PHP features in the project';
    }
}

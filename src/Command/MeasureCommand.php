<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Output\OutputPrinter;
use Entropy\Console\Output\ProgressBar;
use Rector\SwissKnife\Lines\Analyser;
use Rector\SwissKnife\Lines\Finder\MeasureFileFinder;
use Rector\SwissKnife\Lines\OutputFormatter\JsonOutputFormatter;
use Rector\SwissKnife\Lines\OutputFormatter\TextOutputFormatter;

final class MeasureCommand implements CommandInterface
{
    /**
     * @readonly
     */
    private OutputPrinter $outputPrinter;

    /**
     * @readonly
     */
    private Analyser $analyser;

    /**
     * @readonly
     */
    private JsonOutputFormatter $jsonOutputFormatter;

    /**
     * @readonly
     */
    private TextOutputFormatter $textOutputFormatter;

    public function __construct(OutputPrinter $outputPrinter, Analyser $analyser, JsonOutputFormatter $jsonOutputFormatter, TextOutputFormatter $textOutputFormatter)
    {
        $this->outputPrinter = $outputPrinter;
        $this->analyser = $analyser;
        $this->jsonOutputFormatter = $jsonOutputFormatter;
        $this->textOutputFormatter = $textOutputFormatter;
    }

    /**
     * @param string[] $sources One or more paths to measure
     * @param string[] $excludes Paths to exclude
     * @param bool $json Output in JSON format
     * @param bool $short Print short metrics only
     * @param bool $allowVendor Allow /vendor directory to be scanned
     * @param bool $longest Show top 10 longest files
     *
     * @return ExitCode::*
     */
    public function run(
        array $sources,
        array $excludes = [],
        bool $json = false,
        bool $short = false,
        bool $allowVendor = false,
        bool $longest = false
    ): int {
        $filePaths = MeasureFileFinder::find($sources, $excludes, $allowVendor);
        if ($filePaths === []) {
            $this->outputPrinter->error('No files found to scan');
            return ExitCode::ERROR;
        }

        $progressBar = null;
        $progressBarClosure = null;
        if (! $json) {
            $progressBar = new ProgressBar();
            $progressBar->start(count($filePaths));

            $progressBarClosure = static function () use ($progressBar): void {
                $progressBar->advance();
            };
        }

        $measurements = $this->analyser->measureFiles($filePaths, $progressBarClosure);

        if ($progressBar instanceof ProgressBar) {
            $progressBar->finish();
        }

        if ($json) {
            $this->jsonOutputFormatter->printMeasurement($measurements, $short, $longest);
        } else {
            $this->textOutputFormatter->printMeasurement($measurements, $short, $longest);
        }

        return ExitCode::SUCCESS;
    }

    public function getName(): string
    {
        return 'measure';
    }

    public function getDescription(): string
    {
        return 'Measure lines of code and structure size in given path(s)';
    }
}

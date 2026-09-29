<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Lines\FeatureCounter;

use Entropy\Console\ConsoleTable\ConsoleTable;
use Entropy\Console\Output\OutputPrinter;
use Rector\SwissKnife\Lines\FeatureCounter\ValueObject\FeatureCollector;

final readonly class ResultPrinter
{
    public function __construct(
        private OutputPrinter $outputPrinter,
        private ConsoleTable $consoleTable,
    ) {
    }

    public function print(FeatureCollector $featureCollector): void
    {
        $this->outputPrinter->newline(2);

        $rows = [];
        $previousPhpVersion = null;

        foreach ($featureCollector->getPhpFeatures() as $phpFeature) {
            $changedPhpVersion = $previousPhpVersion !== null && $previousPhpVersion !== $phpFeature->getPhpVersion();
            if ($changedPhpVersion) {
                $rows[] = ConsoleTable::SEPARATOR;
            }

            $rows[] = [
                '<fg=yellow>' . $phpFeature->getPhpVersion() . '</>',
                $phpFeature->getName(),
                number_format($phpFeature->getCount(), 0, ',', ' '),
            ];

            $previousPhpVersion = $phpFeature->getPhpVersion();
        }

        $this->consoleTable->render(['PHP version', 'PHP Feature', 'Count'], $rows);

        $this->outputPrinter->newline();
    }
}

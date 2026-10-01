<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Lines\OutputFormatter;

use Entropy\Console\ConsoleTable\ValueObject\TableRow;
use Entropy\Console\ConsoleTable\ValueObject\TableView;
use Entropy\Console\ConsoleTable\ViewRenderer;
use Entropy\Console\Output\OutputPrinter;
use Entropy\Validation\Assert;
use Rector\SwissKnife\Lines\Measurements;
use Rector\SwissKnife\Lines\NumberFormat;

final readonly class TextOutputFormatter
{
    public function __construct(
        private ViewRenderer $viewRenderer,
        private OutputPrinter $outputPrinter,
    ) {
    }

    public function printMeasurement(Measurements $measurements, bool $isShort, bool $showLongestFiles): void
    {
        $this->outputPrinter->newline();

        $this->printFilesAndDirectories($measurements);
        $this->printLinesOfCode($measurements);

        if ($isShort) {
            $this->outputPrinter->newline();

            return;
        }

        $this->printStructure($measurements);
        $this->printMethods($measurements);

        if ($showLongestFiles) {
            $rows = [];
            foreach ($measurements->getLongestFiles() as $filePath => $linesCount) {
                $rows[] = [$filePath, $linesCount];
            }

            $tableRows = $this->formatRows($rows);
            $tableView = new TableView('Longest files', 'Line count', $tableRows);
            $this->viewRenderer->renderTableView($tableView);
        }

        $this->outputPrinter->newline();
    }

    private function printFilesAndDirectories(Measurements $measurements): void
    {
        $tableRows = $this->formatRows([
            ['Directories', $measurements->getDirectoryCount()],
            ['Files', $measurements->getFileCount()],
        ]);

        $tableView = new TableView('Filesystem', 'Count', $tableRows);
        $this->viewRenderer->renderTableView($tableView);
    }

    private function printLinesOfCode(Measurements $measurements): void
    {
        $tableRows = $this->formatRows([
            ['Code', $measurements->getNonCommentLines(), $measurements->getNonCommentLinesRelative()],
            ['Comments', $measurements->getCommentLines(), $measurements->getCommentLinesRelative()],
            ['Total', $measurements->getLines(), 100.0],
        ]);

        $tableView = new TableView('Lines of code', 'Count', $tableRows, true);
        $this->viewRenderer->renderTableView($tableView);
    }

    private function printMethods(Measurements $measurements): void
    {
        if ($measurements->getMethodCount() === 0) {
            return;
        }

        $tableRows = $this->formatRows([
            ['Non-static', $measurements->getNonStaticMethods(), $measurements->getNonStaticMethodsRelative()],
            ['Static', $measurements->getStaticMethods(), $measurements->getStaticMethodsRelative()],
        ]);

        $tableView = new TableView('Method access', 'Count', $tableRows, true);
        $this->viewRenderer->renderTableView($tableView);

        $tableRows = $this->formatRows([
            ['Public', $measurements->getPublicMethods(), $measurements->getPublicMethodsRelative()],
            ['Protected', $measurements->getProtectedMethods(), $measurements->getProtectedMethodsRelative()],
            ['Private', $measurements->getPrivateMethods(), $measurements->getPrivateMethodsRelative()],
        ]);

        $tableView = new TableView('Method visibility', 'Count', $tableRows, true);
        $this->viewRenderer->renderTableView($tableView);
    }

    private function printStructure(Measurements $measurements): void
    {
        $tableRows = $this->formatRows([
            ['Namespaces', $measurements->getNamespaceCount()],
            ['Classes', $measurements->getClassCount()],
            ['* Constants', $measurements->getClassConstantCount(), null, true],
            ['* Methods', $measurements->getMethodCount(), null, true],
            ['Interfaces', $measurements->getInterfaceCount()],
            ['Traits', $measurements->getTraitCount()],
            ['Enums', $measurements->getEnumCount()],
            ['Functions', $measurements->getFunctionCount()],
            ['Closures', $measurements->getClosureCount()],
            ['Global constants', $measurements->getGlobalConstantCount()],
        ]);

        $tableView = new TableView('Structure', 'Count', $tableRows);
        $this->viewRenderer->renderTableView($tableView);
    }

    /**
     * @param array<array{0: string, 1: int|float, 2?: float|null, 3?: bool}> $rows
     * @return TableRow[]
     */
    private function formatRows(array $rows): array
    {
        return array_map(static function (array $row): TableRow {
            Assert::notEmpty($row);

            $prettyNumber = NumberFormat::pretty($row[1]);

            return new TableRow(
                $row[0],
                $prettyNumber,
                isset($row[2]) ? NumberFormat::percent($row[2]) : null,
                $row[3] ?? false
            );
        }, $rows);
    }
}

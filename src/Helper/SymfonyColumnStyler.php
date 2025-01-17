<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Helper;

use ClassLeak202501\Symfony\Component\Console\Helper\Table;
use Composer\Semver\Comparator;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;

/**
 * @see https://symfony.com/doc/current/components/console/helpers/table.html
 */
final class SymfonyColumnStyler
{
    /**
     * @param array<string|null|TableCell> $tableRow
     * @return array<string|null|TableCell>
     */
    public static function styleHighsAndLows(array $tableRow): array
    {
        // set highs and lows
        $stringValues = array_filter($tableRow, 'is_string');
        $stringValues = array_unique($stringValues);

        if (count($stringValues) < 2) {
            // unable to find high + low in 1 item
            return $tableRow;
        }

        // sort from high to low
        usort($stringValues, function (string $firstVersion, string $secondVersion): int {
            return (int) Comparator::lessThan($firstVersion, $secondVersion);
        });

        $highValue = array_shift($stringValues);
        $lowValue = array_pop($stringValues);

        // let's decorate values, high = green, low = red
        return array_map(function (string|TableCell|null $value) use ($highValue, $lowValue): string|TableCell|null {
            if (! is_string($value)) {
                return $value;
            }

            if ($value === $highValue) {
                return self::createGreenTextCell($value);
            }

            if ($value === $lowValue) {
                return self::createRedTextCell($value);
            }

            return $value;
        }, $tableRow);
    }

    public static function createRedCell(string $content): TableCell
    {
        return self::cellWithStyle($content, [
            'bg' => 'red',
            'fg' => 'white',
        ]);
    }

    private static function createRedTextCell(string $content): TableCell
    {
        return self::cellWithStyle($content, [
            'align' => 'right',
            'fg' => 'red',
        ]);
    }

    private static function createGreenTextCell(string $content): TableCell
    {
        return self::cellWithStyle($content, [
            'align' => 'right',
            'fg' => 'green',
        ]);
    }

    /**
     * @param array<string, mixed> $styleOptions
     */
    private static function cellWithStyle(string $content, array $styleOptions): TableCell
    {

        $tableCellStyle = new TableCellStyle($styleOptions);

        return new TableCell($content, [
            'style' => $tableCellStyle,
        ]);
    }
}

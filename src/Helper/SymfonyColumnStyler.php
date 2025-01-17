<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Helper;

use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;

/**
 * @see https://symfony.com/doc/current/components/console/helpers/table.html
 */
final class SymfonyColumnStyler
{
    public static function createRedCell(string $content): TableCell
    {
        $redTableCellStyle = new TableCellStyle([
            'bg' => 'red',
            'fg' => 'white',
        ]);

        return new TableCell($content, [
            'style' => $redTableCellStyle,
        ]);
    }
}

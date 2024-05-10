<?php

declare(strict_types=1);

namespace Rector\SwissKnife\FileSystem;

final class JsonAnalyzer
{
    public function isPrettyPrinted(string $json): bool
    {
        $lines = explode(PHP_EOL, $json);
        if (count($lines) >= 3) {
            return true;
        }

        return false;
    }
}

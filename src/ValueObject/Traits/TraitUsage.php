<?php

declare(strict_types=1);

namespace Rector\SwissKnife\ValueObject\Traits;

final class TraitUsage
{
    /**
     * @param string[] $usingFiles
     */
    public function __construct(
        public string $shortTraitName,
        public int $lineCount,
        public array $usingFiles
    ) {
    }
}

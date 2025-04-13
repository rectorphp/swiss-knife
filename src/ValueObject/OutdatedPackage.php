<?php

declare(strict_types=1);

namespace Rector\SwissKnife\ValueObject;

final readonly class OutdatedPackage
{
    public function __construct(
        public string $name,
        public string $latestVersion,
        public string $installedVersion,
        public string $installedAge
    ) {
    }
}

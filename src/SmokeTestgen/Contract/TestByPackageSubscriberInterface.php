<?php

declare(strict_types=1);

namespace Rector\SwissKnife\SmokeTestgen\Contract;

interface TestByPackageSubscriberInterface
{
    /**
     * @return string[]
     */
    public function getPackageNames(): array;

    public function getTemplateFilePath(): string;
}

<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Jack\ValueObject;

final class ChangedPackageVersion
{
    private string $packageName;

    private string $oldVersion;

    private string $newVersion;

    public function __construct(string $packageName, string $oldVersion, string $newVersion)
    {
        $this->packageName = $packageName;
        $this->oldVersion = $oldVersion;
        $this->newVersion = $newVersion;
    }

    public function getPackageName(): string
    {
        return $this->packageName;
    }

    public function getOldVersion(): string
    {
        return $this->oldVersion;
    }

    public function getNewVersion(): string
    {
        return $this->newVersion;
    }
}

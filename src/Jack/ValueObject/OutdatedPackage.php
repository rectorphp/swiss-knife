<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Jack\ValueObject;

use Entropy\Utils\Regex;

final class OutdatedPackage
{
    private string $name;

    private string $currentVersion;

    private string $composerVersion;

    private bool $isProd;

    private string $latestVersion;

    // nullable on composer 2.7-
    private ?string $currentVersionAge;

    public function __construct(
        string $name,
        string $currentVersion,
        string $composerVersion,
        bool $isProd,
        string $latestVersion,
        ?string $currentVersionAge
    ) {
        $this->name = $name;
        $this->currentVersion = $currentVersion;
        $this->composerVersion = $composerVersion;
        $this->isProd = $isProd;
        $this->latestVersion = $latestVersion;
        $this->currentVersionAge = $currentVersionAge;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCurrentVersion(): string
    {
        return $this->currentVersion;
    }

    public function getComposerVersion(): string
    {
        return $this->composerVersion;
    }

    public function isProd(): bool
    {
        return $this->isProd;
    }

    public function getLatestVersion(): string
    {
        return $this->latestVersion;
    }

    public function getCurrentVersionAge(): ?string
    {
        return $this->currentVersionAge;
    }

    public function isVeryOld(): bool
    {
        if ($this->currentVersionAge === null) {
            return true;
        }

        $matchYears = Regex::match($this->currentVersionAge, '#[3-9] years#');
        return $matchYears !== [];
    }

    public function lastestIsDevBranch(): bool
    {
        if (strncmp($this->latestVersion, 'dev-', strlen('dev-')) === 0) {
            return true;
        }

        return strpos($this->latestVersion, '-dev') !== false;
    }
}

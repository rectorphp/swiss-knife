<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Jack\ValueObject\ComposerProcessorResult;

use Entropy\Validation\Assert;
use Rector\SwissKnife\Jack\ValueObject\ChangedPackageVersion;

final class ChangedPackageVersionsResult
{
    private string $composerJsonContents;

    /**
     * @var ChangedPackageVersion[]
     */
    private array $changedPackageVersions;

    /**
     * @param ChangedPackageVersion[] $changedPackageVersions
     */
    public function __construct(string $composerJsonContents, array $changedPackageVersions)
    {
        Assert::allIsInstanceOf($changedPackageVersions, ChangedPackageVersion::class);

        $this->composerJsonContents = $composerJsonContents;
        $this->changedPackageVersions = $changedPackageVersions;
    }

    public function getComposerJsonContents(): string
    {
        return $this->composerJsonContents;
    }

    /**
     * @return ChangedPackageVersion[]
     */
    public function getChangedPackageVersions(): array
    {
        return $this->changedPackageVersions;
    }
}

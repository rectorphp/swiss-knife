<?php

declare(strict_types=1);

namespace Rector\SwissKnife\ValueObject;

use Webmozart\Assert\Assert;

final class ComposerJsonCollection
{
    /**
     * @param ComposerJson[] $composerJsons
     */
    public function __construct(
        private readonly array $composerJsons
    ) {
        Assert::allIsInstanceOf($composerJsons, ComposerJson::class);
    }

    /**
     * @return string[]
     */
    public function getRepositoryNames(): array
    {
        $repositoryNames = [];
        foreach ($this->composerJsons as $composerJson) {
            $repositoryNames[] = $composerJson->getRepositoryName();
        }

        return $repositoryNames;
    }

    /**
     * @return string[]
     */
    public function getRequiredPackageNames(): array
    {
        $requiredPackageNames = [];
        foreach ($this->composerJsons as $composerJson) {
            $requiredPackageNames = array_merge($requiredPackageNames, $composerJson->getRequiredPackageNames());
        }

        $uniquePackageNames = array_unique($requiredPackageNames);
        return $this->filterOutExtensions($uniquePackageNames);
    }

    /**
     * @param string[] $uniquePackageNames
     * @return string[]
     */
    private function filterOutExtensions(array $uniquePackageNames): array
    {
        return array_filter($uniquePackageNames, fn (string $packageName) => ! str_starts_with($packageName, 'ext-'));
    }
}

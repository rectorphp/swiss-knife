<?php

declare (strict_types=1);
namespace Rector\SwissKnife\ValueObject;

use SwissKnife202501\Webmozart\Assert\Assert;
final class ComposerJsonCollection
{
    /**
     * @var ComposerJson[]
     * @readonly
     */
    private $composerJsons;
    /**
     * @param ComposerJson[] $composerJsons
     */
    public function __construct(array $composerJsons)
    {
        $this->composerJsons = $composerJsons;
        Assert::allIsInstanceOf($composerJsons, \Rector\SwissKnife\ValueObject\ComposerJson::class);
    }
    /**
     * @return string[]
     */
    public function getRepositoryNames() : array
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
    public function getRequiredPackageNames() : array
    {
        $requiredPackageNames = [];
        foreach ($this->composerJsons as $composerJson) {
            $requiredPackageNames = \array_merge($requiredPackageNames, $composerJson->getRequiredPackageNames());
        }
        $uniquePackageNames = \array_unique($requiredPackageNames);
        return $this->filterOutExtensions($uniquePackageNames);
    }
    /**
     * @return ComposerJson[]
     */
    public function all() : array
    {
        return $this->composerJsons;
    }
    /**
     * @param string[] $uniquePackageNames
     * @return string[]
     */
    private function filterOutExtensions(array $uniquePackageNames) : array
    {
        return \array_filter($uniquePackageNames, function (string $packageName) {
            return \strncmp($packageName, 'ext-', \strlen('ext-')) !== 0;
        });
    }
}

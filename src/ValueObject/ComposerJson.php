<?php

declare (strict_types=1);
namespace Rector\SwissKnife\ValueObject;

use SwissKnife202502\Nette\Utils\Strings;
use SwissKnife202502\Webmozart\Assert\Assert;
final class ComposerJson
{
    /**
     * @readonly
     * @var string
     */
    private $repositoryGit;
    /**
     * @var array<string, mixed>
     * @readonly
     */
    private $composerJson;
    /**
     * @param array<string, mixed> $composerJson
     */
    public function __construct(string $repositoryGit, array $composerJson)
    {
        $this->repositoryGit = $repositoryGit;
        $this->composerJson = $composerJson;
    }
    public function getRepositoryName() : string
    {
        $match = Strings::match($this->repositoryGit, '#(?<repository_name>[^/]+)\\.git$#');
        Assert::isArray($match);
        Assert::keyExists($match, 'repository_name');
        return $match['repository_name'];
    }
    /**
     * @return string[]
     */
    public function getRequiredPackageNames() : array
    {
        $bothRequires = $this->getBothRequires();
        return \array_keys($bothRequires);
    }
    public function getPackageVersion(string $packageName) : ?string
    {
        return $this->getBothRequires()[$packageName] ?? null;
    }
    /**
     * @return string[]
     */
    public function getRequiredRepositories() : array
    {
        $repositories = [];
        $repositoriesData = $this->composerJson['repositories'] ?? [];
        foreach ($repositoriesData as $repositoryData) {
            if (!isset($repositoryData['url'])) {
                continue;
            }
            // not a git repository reference
            if (\substr_compare((string) $repositoryData['url'], '.git', -\strlen('.git')) !== 0) {
                continue;
            }
            $repositories[] = $repositoryData['url'];
        }
        return $repositories;
    }
    /**
     * @return array<string, string>
     */
    private function getBothRequires() : array
    {
        return \array_merge($this->composerJson['require'] ?? [], $this->composerJson['require-dev'] ?? []);
    }
}

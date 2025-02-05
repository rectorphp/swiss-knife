<?php

declare(strict_types=1);

namespace Rector\SwissKnife\ValueObject;

use Nette\Utils\Strings;
use Webmozart\Assert\Assert;

final readonly class ComposerJson
{
    /**
     * @param array<string, mixed> $composerJson
     */
    public function __construct(
        private string $repositoryGit,
        private array $composerJson
    ) {
    }

    public function getRepositoryName(): string
    {
        $match = Strings::match($this->repositoryGit, '#(?<repository_name>[^/]+)\.git$#');
        Assert::isArray($match);
        Assert::keyExists($match, 'repository_name');

        return $match['repository_name'];
    }

    /**
     * @return string[]
     */
    public function getRequiredPackageNames(): array
    {
        $bothRequires = $this->getBothRequires();
        return array_keys($bothRequires);
    }

    public function getPackageVersion(string $packageName): ?string
    {
        return $this->getBothRequires()[$packageName] ?? null;
    }

    /**
     * @return string[]
     */
    public function getRequiredRepositories(): array
    {
        $repositories = [];

        $repositoriesData = $this->composerJson['repositories'] ?? [];
        foreach ($repositoriesData as $repositoryData) {
            if (! isset($repositoryData['url'])) {
                continue;
            }

            // not a git repository reference
            if (! str_ends_with((string) $repositoryData['url'], '.git')) {
                continue;
            }

            $repositories[] = $repositoryData['url'];
        }

        return $repositories;
    }

    /**
     * @return array<string, string>
     */
    private function getBothRequires(): array
    {
        return array_merge($this->composerJson['require'] ?? [], $this->composerJson['require-dev'] ?? []);
    }
}

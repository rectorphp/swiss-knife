<?php

declare(strict_types=1);

namespace Rector\SwissKnife\ValueObject;

use Nette\Utils\Json;
use Nette\Utils\Strings;
use Webmozart\Assert\Assert;

final class ComposerJson
{
    /**
     * @var array<string, mixed>
     */
    private readonly array $json;

    public function __construct(
        private readonly string $repositoryGit,
        string $composerJsonContents
    ) {
        $this->json = Json::decode($composerJsonContents, forceArrays: true);
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
     * @return array<string, string>
     */
    private function getBothRequires(): array
    {
        return array_merge($this->json['require'] ?? [], $this->json['require-dev'] ?? []);
    }
}

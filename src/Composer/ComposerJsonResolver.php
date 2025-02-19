<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Composer;

use SwissKnife202502\Nette\Utils\FileSystem;
use SwissKnife202502\Nette\Utils\Json;
use Rector\SwissKnife\ValueObject\ComposerJson;
use Rector\SwissKnife\ValueObject\ComposerJsonCollection;
use SwissKnife202502\Symfony\Component\Console\Style\SymfonyStyle;
use SwissKnife202502\Webmozart\Assert\Assert;
final class ComposerJsonResolver
{
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @var string
     */
    private const TEMP_PROJECT_COMPOSER_JSON = 'temp-project-composer.json';
    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }
    /**
     * @param string[] $repositories
     */
    public function resolveFromRepositories(array $repositories) : ComposerJsonCollection
    {
        Assert::allString($repositories);
        $projectsComposerJsons = [];
        foreach ($repositories as $repository) {
            $projectsComposerJson = $this->getComposerJsonForRepository($repository, \true);
            $projectsComposerJsons[] = new ComposerJson($repository, $projectsComposerJson);
        }
        // tidy up temporary file
        FileSystem::delete(self::TEMP_PROJECT_COMPOSER_JSON);
        return new ComposerJsonCollection($projectsComposerJsons);
    }
    /**
     * @return array{"require": mixed[], "require-dev": mixed[]}
     */
    private function getComposerJsonForRepository(string $repository, bool $useCache) : array
    {
        // allow cache to avoid long staling
        $cacheFilePath = \sys_get_temp_dir() . '/multi-composer-json/' . \md5($repository) . '.json';
        if ($useCache && \file_exists($cacheFilePath)) {
            $fileContents = FileSystem::read($cacheFilePath);
            return Json::decode($fileContents, Json::FORCE_ARRAY);
        }
        $this->symfonyStyle->writeln('Loading for ' . $repository);
        // clone only "composer.json" file
        \exec(\sprintf('git archive --remote=%s HEAD composer.json | tar -xO composer.json > %s', $repository, self::TEMP_PROJECT_COMPOSER_JSON));
        $projectsComposerJsonContents = FileSystem::read(self::TEMP_PROJECT_COMPOSER_JSON);
        // unset all but require and require-dev sections
        Assert::string($projectsComposerJsonContents);
        Assert::notEmpty($projectsComposerJsonContents);
        $projectsComposerJson = Json::decode($projectsComposerJsonContents, Json::FORCE_ARRAY);
        // store only necessary data
        $bareComposerJson = ['require' => $projectsComposerJson['require'] ?? [], 'require-dev' => $projectsComposerJson['require-dev'] ?? [], 'repositories' => $projectsComposerJson['repositories'] ?? []];
        FileSystem::write($cacheFilePath, Json::encode($bareComposerJson, \true));
        return $bareComposerJson;
    }
}

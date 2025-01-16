<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Composer;

use Nette\Utils\FileSystem;
use Rector\SwissKnife\ValueObject\ComposerJson;
use Webmozart\Assert\Assert;

final class ComposerJsonResolver
{
    /**
     * @var string
     */
    private const TEMP_PROJECT_COMPOSER_JSON = 'temp-project-composer.json';

    /**
     * @param string[] $repositories
     * @return ComposerJson[]
     */
    public function resolveFromRepositories(array $repositories): array
    {
        Assert::allString($repositories);

        $projectsComposerJsons = [];
        foreach ($repositories as $repository) {
            // clones only "composer.json" file
            exec(sprintf(
                'git archive --remote=%s HEAD composer.json | tar -xO composer.json > %s',
                $repository,
                self::TEMP_PROJECT_COMPOSER_JSON
            ));

            $projectsComposerJsonContents = FileSystem::read(self::TEMP_PROJECT_COMPOSER_JSON);

            Assert::string($projectsComposerJsonContents);
            Assert::notEmpty($projectsComposerJsonContents);

            $projectsComposerJsons[] = new ComposerJson($repository, $projectsComposerJsonContents);
        }

        // tidy up temporary file
        FileSystem::delete(self::TEMP_PROJECT_COMPOSER_JSON);

        return $projectsComposerJsons;
    }
}

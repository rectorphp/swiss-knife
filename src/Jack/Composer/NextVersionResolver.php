<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Jack\Composer;

use Composer\Semver\VersionParser;
use Entropy\Utils\Regex;
use Rector\SwissKnife\Jack\Exception\ShouldNotHappenException;

/**
 * @see \Rector\SwissKnife\Jack\Tests\Composer\NextVersionResolver\NextVersionResolverTest
 */
final class NextVersionResolver
{
    private const MAJOR = 'major';

    private const MINOR = 'minor';

    private VersionParser $versionParser;

    public function __construct(VersionParser $versionParser)
    {
        $this->versionParser = $versionParser;
    }

    public function resolve(string $packageName, string $composerVersion): string
    {
        $constraint = $this->versionParser->parseConstraints($composerVersion);

        $nextBound = $constraint->getUpperBound();
        $matchVersion = Regex::match(
            $nextBound->getVersion(),
            '#^(?<' . self::MAJOR . '>\d+)\.(?<' . self::MINOR . '>\d+)#'
        );

        if ($matchVersion === []) {
            throw new ShouldNotHappenException(
                sprintf('Unable to parse major and minor value from composer version "%s"', $composerVersion)
            );
        }

        // special case for "symfony/*" packages as version jump is huge there
        if (strpos($composerVersion, '*') !== false || strncmp($packageName, 'symfony/', strlen('symfony/')) === 0) {
            return $matchVersion[self::MAJOR] . '.' . $matchVersion[self::MINOR] . '.*';
        }

        return '^' . $matchVersion[self::MAJOR] . '.' . $matchVersion[self::MINOR];
    }
}

<?php

declare(strict_types=1);

namespace Rector\SwissKnife\PHPStan;

use Nette\Utils\Strings;
use Rector\SwissKnife\ValueObject\ClassConstMatch;
use Rector\SwissKnife\ValueObject\PublicAndProtectedClassConstants;

final class ClassConstantResultAnalyser
{
    /**
     * @var string
     * @see https://regex101.com/r/VR8VUD/1
     */
    private const PRIVATE_CONSTANT_MESSAGE_REGEX = '#Access to private constant (?<constant_name>.*?) of class (?<class_name>[\w\\\\]+)#';

    /**
     * @var string
     * @see https://regex101.com/r/pRzdnw/1
     */
    private const PROTECTED_CONSTANT_MESSAGE_REGEX = '#Access to undefined constant (?<class_name>[\w\\\\]+)::(?<constant_name>.*?)#';

    /**
     * @param mixed[] $phpstanResult
     */
    public function analyseResult(array $phpstanResult): PublicAndProtectedClassConstants
    {
        $publicClassConstMatches = [];
        $protectedClassConstMatches = [];

        foreach ($phpstanResult['files'] as $fileDetail) {
            foreach ($fileDetail['messages'] as $messageError) {
                $publicClassConstMatch = $this->matchPublicClassConstMatch($messageError['message']);
                if ($publicClassConstMatch instanceof ClassConstMatch) {
                    $publicClassConstMatches[] = $publicClassConstMatch;
                }

                $protectedClassConstMatch = $this->matchProtectedClassConstMatch($messageError['message']);
                if ($protectedClassConstMatch instanceof ClassConstMatch) {
                    $protectedClassConstMatches[] = $protectedClassConstMatch;
                }
            }
        }

        return new PublicAndProtectedClassConstants($publicClassConstMatches, $protectedClassConstMatches);
    }

    private function matchProtectedClassConstMatch(string $errorMessage): ?ClassConstMatch
    {
        return $this->matchClassConstMatchWithRegex($errorMessage, self::PROTECTED_CONSTANT_MESSAGE_REGEX);
    }

    private function matchPublicClassConstMatch(string $errorMessage): ?ClassConstMatch
    {
        return $this->matchClassConstMatchWithRegex($errorMessage, self::PRIVATE_CONSTANT_MESSAGE_REGEX);
    }

    private function matchClassConstMatchWithRegex(string $errorMessage, string $regex): ?ClassConstMatch
    {
        $match = Strings::match($errorMessage, $regex);
        if (! isset($match['constant_name'], $match['class_name'])) {
            return null;
        }

        /** @var class-string $className */
        $className = (string) $match['class_name'];
        return new ClassConstMatch($className, (string) $match['constant_name']);
    }
}

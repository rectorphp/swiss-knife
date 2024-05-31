<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Helpers;

use Nette\Utils\Strings;

final class ClassNameResolver
{
    /**
     * @var string
     * @see https://regex101.com/r/tttAOn/1
     */
    private const NAMESPACE_REGEX = '#\bnamespace\s+(?<namespace>[\w\\\\]+);#';

    /**
     * @var string
     * @see https://regex101.com/r/B7LvXE/1
     */
    private const SHORT_CLASS_NAME_REGEX = '#\bclass\s+(?<short_class_name>[A-Z][A-Za-z]+)#';

    /**
     * @return class-string|null
     */
    public static function resolveFromFileContents(string $fileContents): ?string
    {
        $namespaceMatch = Strings::match($fileContents, self::NAMESPACE_REGEX);
        $classMatch = Strings::match($fileContents, self::SHORT_CLASS_NAME_REGEX);

        // short class must exist
        if (!isset($classMatch['short_class_name'])) {
            return null;
        }

        return ($namespaceMatch['namespace'] ?? '') . $classMatch['short_class_name'];
    }
}

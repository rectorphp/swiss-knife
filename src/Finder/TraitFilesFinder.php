<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Finder;

use Nette\Utils\Strings;
use Rector\SwissKnife\ValueObject\FileInfo;
use Webmozart\Assert\Assert;

final class TraitFilesFinder
{
    /**
     * @param string[] $directories
     * @return FileInfo[]
     */
    public function findTraitUsages(array $directories): array
    {
        Assert::allString($directories);

        return FileScanner::scan($directories, static function (FileInfo $fileInfo): bool {
            if ($fileInfo->getExtension() !== 'php') {
                return false;
            }

            return str_contains($fileInfo->getContents(), '    use ');
        });
    }

    /**
     * @param string[] $directories
     * @return FileInfo[]
     */
    public function find(array $directories): array
    {
        Assert::allString($directories);

        return FileScanner::scan($directories, static function (FileInfo $fileInfo): bool {
            if ($fileInfo->getExtension() !== 'php') {
                return false;
            }

            $normalizedPath = str_replace('\\', '/', (string) $fileInfo->getRealPath());
            if (str_contains($normalizedPath, '/Entity/') || str_contains($normalizedPath, '/Document/')) {
                return false;
            }

            return (bool) Strings::match($fileInfo->getContents(), '#^trait\s#m');
        });
    }
}

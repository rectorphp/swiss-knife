<?php

declare(strict_types=1);

namespace Migrify\EasyCI\Finder;

use Migrify\EasyCI\ValueObject\SrcAndTestsDirectories;
use Nette\Utils\Strings;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Symplify\EasyTesting\PHPUnit\StaticPHPUnitEnvironment;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SrcTestsDirectoriesFinder
{
    /**
     * @see https://regex101.com/r/KkSmFS/1
     */
    private const SRC_ONLY_REGEX = '#\bsrc\b#';

    /**
     * @see https://regex101.com/r/wzPJ72/2
     */
    private const TESTS_ONLY_REGEX = '#\btests\b#';

    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    public function __construct(FinderSanitizer $finderSanitizer)
    {
        $this->finderSanitizer = $finderSanitizer;
    }

    /**
     * @param string[] $directories
     */
    public function findSrcAndTestsDirectories(array $directories): ?SrcAndTestsDirectories
    {
        $fileInfos = $this->findInDirectories($directories);
        if ($fileInfos === []) {
            return null;
        }

        $srcDirectories = [];
        $testsDirectories = [];

        foreach ($fileInfos as $fileInfo) {
            if ($fileInfo->endsWith('tests') && ! Strings::contains($fileInfo->getRealPath(), 'src')) {
                $testsDirectories[] = $fileInfo;
            } elseif ($fileInfo->endsWith('src') && (! Strings::contains(
                $fileInfo->getRealPath(),
                'tests'
            ) || StaticPHPUnitEnvironment::isPHPUnitRun())) {
                $srcDirectories[] = $fileInfo;
            }
        }

        return new SrcAndTestsDirectories($srcDirectories, $testsDirectories);
    }

    /**
     * @return SmartFileInfo[]
     */
    private function findInDirectories(array $directories): array
    {
        $existingDirectories = $this->filterExistingDirectories($directories);
        if ($existingDirectories === []) {
            return [];
        }

        $finder = (new Finder())
            ->directories()
            ->name('#(src|tests)$#')
            ->exclude('Fixture')
            ->in($existingDirectories)
            // exclude tests/src directory nested in /tests, e.g. real project for testing
            ->filter(function (SplFileInfo $fileInfo) {
                $srcCounter = count(Strings::matchAll($fileInfo->getPathname(), self::SRC_ONLY_REGEX));
                $testsCounter = count(Strings::matchAll($fileInfo->getPathname(), self::TESTS_ONLY_REGEX));

                if ($srcCounter > 1) {
                    return false;
                }

                if ($testsCounter > 1) {
                    return false;
                }

                return true;
            });

        return $this->finderSanitizer->sanitize($finder);
    }

    /**
     * @param string[] $directories
     * @return string[]
     */
    private function filterExistingDirectories(array $directories): array
    {
        return array_filter($directories, function (string $directory) {
            return file_exists($directory);
        });
    }
}

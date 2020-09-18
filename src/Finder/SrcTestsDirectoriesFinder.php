<?php

declare(strict_types=1);

namespace Migrify\EasyCI\Finder;

use Migrify\EasyCI\ValueObject\SrcAndTestsDirectories;
use Nette\Utils\Strings;
use Symfony\Component\Finder\Finder;
use Symplify\EasyTesting\PHPUnit\StaticPHPUnitEnvironment;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SrcTestsDirectoriesFinder
{
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
            ->in($existingDirectories);

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

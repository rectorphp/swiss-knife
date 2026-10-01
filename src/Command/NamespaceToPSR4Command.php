<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Output\OutputPrinter;
use Entropy\FileSystem\FileFinder;
use Entropy\FileSystem\FileInfo;
use Entropy\Utils\FileSystem;
use Entropy\Utils\Regex;

/**
 * @see \Rector\SwissKnife\Tests\Command\NamespaceToPSR4CommandTest
 */
final class NamespaceToPSR4Command implements CommandInterface
{
    /**
     * @readonly
     */
    private OutputPrinter $outputPrinter;

    public function __construct(OutputPrinter $outputPrinter)
    {
        $this->outputPrinter = $outputPrinter;
    }

    /**
     * @param string $path Single directory path to ensure namespace matches, e.g. "tests"
     * @param string $namespaceRoot Namespace root for files in provided path, e.g. "App\\Tests"
     *
     * @return ExitCode::*
     */
    public function run(string $path, string $namespaceRoot): int
    {
        $namespaceRoot = rtrim($namespaceRoot, '\\');
        $namespaceRoot = str_replace('\\\\', '\\', $namespaceRoot);

        $fileInfos = $this->findFilesInPath($path);

        $changedFilesCount = 0;

        foreach ($fileInfos as $fileInfo) {
            $expectedNamespace = $this->resolveExpectedNamespace($namespaceRoot, $fileInfo);
            $expectedNamespaceLine = 'namespace ' . $expectedNamespace . ';';

            // 1. got the correct namespace
            if (strpos($fileInfo->getContents(), $expectedNamespaceLine) !== false) {
                continue;
            }

            // 2. incorrect namespace found
            $this->outputPrinter->yellow(sprintf(
                'File "%s" fixed to expected namespace "%s"',
                $fileInfo->getRelativePathname(),
                $expectedNamespace
            ));

            // 3. replace
            $correctedContents = Regex::replace(
                $fileInfo->getContents(),
                '#namespace (.*?);#',
                $expectedNamespaceLine
            );

            // 4. print file
            FileSystem::write($fileInfo->getRealPath(), $correctedContents);

            ++$changedFilesCount;
        }

        if ($changedFilesCount === 0) {
            $fileCount = count($fileInfos);
            $this->outputPrinter->success(sprintf(
                'All %d file%s have correct namespace',
                $fileCount,
                $fileCount === 1 ? '' : 's'
            ));
        } else {
            $this->outputPrinter->success(sprintf(
                'Fixed %d file%s',
                $changedFilesCount,
                $changedFilesCount === 1 ? '' : 's'
            ));
        }

        return ExitCode::SUCCESS;
    }

    public function getName(): string
    {
        return 'namespace-to-psr-4';
    }

    public function getDescription(): string
    {
        return 'Change namespace in your PHP files to match PSR-4 root';
    }

    /**
     * @return FileInfo[]
     */
    private function findFilesInPath(string $path): array
    {
        return FileFinder::find([$path], static function (FileInfo $fileInfo): bool {
            if ($fileInfo->getExtension() !== 'php') {
                return false;
            }

            // filter classes
            return strpos($fileInfo->getContents(), 'class ') !== false;
        });
    }

    private function resolveExpectedNamespace(string $namespaceRoot, FileInfo $fileInfo): string
    {
        $relativePathNamespace = str_replace('/', '\\', $fileInfo->getRelativePath());
        if ($relativePathNamespace === '') {
            return $namespaceRoot;
        }

        return $namespaceRoot . '\\' . $relativePathNamespace;
    }
}

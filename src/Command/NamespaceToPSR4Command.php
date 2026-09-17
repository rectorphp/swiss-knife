<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Output\OutputPrinter;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Rector\SwissKnife\Finder\FileScanner;
use Rector\SwissKnife\ValueObject\FileInfo;

/**
 * @see \Rector\SwissKnife\Tests\Command\NamespaceToPSR4CommandTest
 */
final readonly class NamespaceToPSR4Command implements CommandInterface
{
    public function __construct(
        private OutputPrinter $outputPrinter,
    ) {
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
            if (\str_contains($fileInfo->getContents(), $expectedNamespaceLine)) {
                continue;
            }

            // 2. incorrect namespace found
            $this->outputPrinter->yellow(sprintf(
                'File "%s"%s fixed to expected namespace "%s"',
                $fileInfo->getRelativePathname(),
                PHP_EOL,
                $expectedNamespace
            ));

            // 3. replace
            $correctedContents = Strings::replace(
                $fileInfo->getContents(),
                '#namespace (.*?);#',
                $expectedNamespaceLine
            );

            // 4. print file
            FileSystem::write($fileInfo->getRealPath(), $correctedContents, null);

            ++$changedFilesCount;
        }

        if ($changedFilesCount === 0) {
            $this->outputPrinter->success(sprintf('All %d files have correct namespace', count($fileInfos)));
        } else {
            $this->outputPrinter->success(sprintf('Fixed %d files', $changedFilesCount));
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
        return FileScanner::scan([$path], static function (FileInfo $fileInfo): bool {
            if ($fileInfo->getExtension() !== 'php') {
                return false;
            }

            // filter classes
            return str_contains($fileInfo->getContents(), 'class ');
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

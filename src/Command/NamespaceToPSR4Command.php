<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Command;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class NamespaceToPSR4Command extends Command
{
    public function __construct(
        private readonly SymfonyStyle $symfonyStyle,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('namespace-to-psr-4');

        $this->setDescription('Change namespace in your PHP files to match PSR-4 root');

        $this->addArgument(
            'path',
            InputArgument::REQUIRED,
            'Single directory path to ensure namespace matches, e.g. "tests"'
        );

        $this->addOption(
            'namespace-root',
            null,
            InputOption::VALUE_REQUIRED,
            'Namespace root for files in provided path, e.g. "App\\Tests"'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = (string) $input->getArgument('path');
        $namespaceRoot = (string) $input->getArgument('namespace-root');

        $fileInfos = $this->findFilesInPath($path);

        $changedFilesCount = 0;

        /** @var SplFileInfo $fileInfo */
        foreach ($fileInfos as $fileInfo) {
            $expectedNamespace = $this->resolveExpectedNamespace($namespaceRoot, $fileInfo);
            $expectedNamespaceLine = 'namespace ' . $expectedNamespace . ';';

            // 1. got the correct namespace
            if (\str_contains($fileInfo->getContents(), $expectedNamespaceLine)) {
                continue;
            }

            // 2. incorrect namespace found
            $this->symfonyStyle->note(sprintf(
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
            FileSystem::write($fileInfo->getRealPath(), $correctedContents);

            ++$changedFilesCount;
        }

        if ($changedFilesCount === 0) {
            $this->symfonyStyle->success(sprintf('All %d files have correct namespace', count($fileInfos)));
        } else {
            $this->symfonyStyle->success(sprintf('Fixed %d files', $changedFilesCount));
        }

        return self::SUCCESS;
    }

    /**
     * @return SplFileInfo[]
     */
    private function findFilesInPath(string $path): array
    {
        $finder = Finder::create()
            ->files()
            ->in([$path])
            ->name('*.php')
            ->sortByName()
            ->filter(static fn (SplFileInfo $fileInfo): bool =>
                // filter classes
                str_contains($fileInfo->getContents(), 'class '));

        return iterator_to_array($finder->getIterator());
    }

    private function resolveExpectedNamespace(string $namespaceRoot, SplFileInfo $fileInfo): string
    {
        $relativePathNamespace = str_replace('/', '\\', $fileInfo->getRelativePath());
        return $namespaceRoot . '\\' . $relativePathNamespace;
    }
}

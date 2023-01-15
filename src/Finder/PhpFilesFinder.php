<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Finder;

use SplFileInfo;
use Symfony\Component\Console\Input\InputInterface;
use Symplify\EasyCI\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\SmartFileSystem\Finder\SmartFinder;
use Webmozart\Assert\Assert;

final class PhpFilesFinder
{
    public function __construct(
        private readonly SmartFinder $smartFinder,
        private readonly ParameterProvider $parameterProvider
    ) {
    }

    /**
     * @return string[]
     */
    public function findPhpFiles(InputInterface $input): array
    {
        $excludedCheckPaths = $this->parameterProvider->provideArrayParameter(Option::EXCLUDED_CHECK_PATHS);

        $paths = (array) $input->getArgument(Option::SOURCES);

        // fallback to config paths
        if ($paths === []) {
            $paths = $this->parameterProvider->provideArrayParameter(Option::PATHS);
        }

        $fileInfos = $this->smartFinder->find($paths, '*.php', $excludedCheckPaths);

        $filePaths = array_map(
            static fn (SplFileInfo $fileInfo): string => $fileInfo->getRealPath(),
            $fileInfos
        );

        Assert::allString($filePaths);
        return $filePaths;
    }
}

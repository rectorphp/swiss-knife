<?php

declare(strict_types=1);

namespace Rector\SwissKnife\PhpParser;

use PhpParser\NodeTraverser;
use Rector\SwissKnife\Contract\ClassConstantFetchInterface;
use Rector\SwissKnife\Exception\NotImplementedYetException;
use Rector\SwissKnife\Exception\ShouldNotHappenException;
use Rector\SwissKnife\PhpParser\NodeVisitor\FindClassConstFetchNodeVisitor;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @see \Rector\SwissKnife\Tests\PhpParser\ClassConstantFetchFinder\ClassConstantFetchFinderTest
 */
final class ClassConstantFetchFinder
{
    public function __construct(
        private CachedPhpParser $cachedPhpParser,
        private SymfonyStyle $symfonyStyle,
    ) {
    }

    /**
     * @param SplFileInfo[] $phpFileInfos
     * @return ClassConstantFetchInterface[]
     */
    public function find(array $phpFileInfos, ProgressBar $progressBar, bool $isDebug): array
    {
        $nodeTraverser = new NodeTraverser();

        $findClassConstFetchNodeVisitor = new FindClassConstFetchNodeVisitor();
        $nodeTraverser->addVisitor($findClassConstFetchNodeVisitor);

        foreach ($phpFileInfos as $phpFileInfo) {
            if ($isDebug) {
                $this->symfonyStyle->writeln('Processing ' . $phpFileInfo->getRealPath());
            }

            $fileStmts = $this->cachedPhpParser->parseFile($phpFileInfo->getRealPath());

            try {
                $nodeTraverser->traverse($fileStmts);
            } catch (ShouldNotHappenException|NotImplementedYetException $exception) {
                // render debug contents if verbose
                if ($isDebug) {
                    $this->symfonyStyle->error($exception->getMessage());
                }
            }

            if ($isDebug === false) {
                $progressBar->advance();
            }
        }

        if ($isDebug === false) {
            $progressBar->finish();
        }

        return $findClassConstFetchNodeVisitor->getClassConstantFetches();
    }
}

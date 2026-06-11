<?php

declare(strict_types=1);

namespace Rector\SwissKnife\PhpParser\Finder;

use Entropy\Console\Output\OutputPrinter;
use Entropy\Console\Output\ProgressBar;
use Rector\SwissKnife\Contract\ClassConstantFetchInterface;
use Rector\SwissKnife\Exception\NotImplementedYetException;
use Rector\SwissKnife\Exception\ShouldNotHappenException;
use Rector\SwissKnife\PhpParser\CachedPhpParser;
use Rector\SwissKnife\PhpParser\NodeTraverserFactory;
use Rector\SwissKnife\PhpParser\NodeVisitor\FindClassConstFetchNodeVisitor;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @see \Rector\SwissKnife\Tests\PhpParser\ClassConstantFetchFinder\ClassConstantFetchFinderTest
 */
final readonly class ClassConstantFetchFinder
{
    public function __construct(
        private CachedPhpParser $cachedPhpParser,
        private OutputPrinter $outputPrinter,
    ) {
    }

    /**
     * @param SplFileInfo[] $phpFileInfos
     * @return ClassConstantFetchInterface[]
     */
    public function find(array $phpFileInfos, ProgressBar $progressBar, bool $isDebug): array
    {
        $findClassConstFetchNodeVisitor = new FindClassConstFetchNodeVisitor();
        $nodeTraverser = NodeTraverserFactory::create($findClassConstFetchNodeVisitor);

        foreach ($phpFileInfos as $phpFileInfo) {
            if ($isDebug) {
                $this->outputPrinter->writeln('Processing ' . $phpFileInfo->getRealPath());
            }

            $fileStmts = $this->cachedPhpParser->parseFile($phpFileInfo->getRealPath());

            try {
                $nodeTraverser->traverse($fileStmts);
            } catch (ShouldNotHappenException|NotImplementedYetException $exception) {
                // render debug contents if verbose
                if ($isDebug) {
                    $this->outputPrinter->error($exception->getMessage());
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

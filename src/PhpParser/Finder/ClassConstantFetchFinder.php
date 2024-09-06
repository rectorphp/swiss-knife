<?php

declare (strict_types=1);
namespace Rector\SwissKnife\PhpParser\Finder;

use Rector\SwissKnife\Contract\ClassConstantFetchInterface;
use Rector\SwissKnife\Exception\NotImplementedYetException;
use Rector\SwissKnife\Exception\ShouldNotHappenException;
use Rector\SwissKnife\PhpParser\CachedPhpParser;
use Rector\SwissKnife\PhpParser\NodeTraverserFactory;
use Rector\SwissKnife\PhpParser\NodeVisitor\FindClassConstFetchNodeVisitor;
use SwissKnife202409\Symfony\Component\Console\Helper\ProgressBar;
use SwissKnife202409\Symfony\Component\Console\Style\SymfonyStyle;
use SwissKnife202409\Symfony\Component\Finder\SplFileInfo;
/**
 * @see \Rector\SwissKnife\Tests\PhpParser\ClassConstantFetchFinder\ClassConstantFetchFinderTest
 */
final class ClassConstantFetchFinder
{
    /**
     * @var \Rector\SwissKnife\PhpParser\CachedPhpParser
     */
    private $cachedPhpParser;
    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    public function __construct(CachedPhpParser $cachedPhpParser, SymfonyStyle $symfonyStyle)
    {
        $this->cachedPhpParser = $cachedPhpParser;
        $this->symfonyStyle = $symfonyStyle;
    }
    /**
     * @param SplFileInfo[] $phpFileInfos
     * @return ClassConstantFetchInterface[]
     */
    public function find(array $phpFileInfos, ProgressBar $progressBar, bool $isDebug) : array
    {
        $findClassConstFetchNodeVisitor = new FindClassConstFetchNodeVisitor();
        $nodeTraverser = NodeTraverserFactory::create($findClassConstFetchNodeVisitor);
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
            if ($isDebug === \false) {
                $progressBar->advance();
            }
        }
        if ($isDebug === \false) {
            $progressBar->finish();
        }
        return $findClassConstFetchNodeVisitor->getClassConstantFetches();
    }
}

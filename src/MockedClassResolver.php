<?php

declare(strict_types=1);

namespace Rector\SwissKnife;

use PhpParser\NodeTraverser;
use Rector\SwissKnife\Finder\PhpFilesFinder;
use Rector\SwissKnife\PhpParser\CachedPhpParser;
use Rector\SwissKnife\PhpParser\NodeVisitor\MockedClassNameCollectingNodeVisitor;
use Symfony\Component\Finder\SplFileInfo;
use Webmozart\Assert\Assert;

final readonly class MockedClassResolver
{
    public function __construct(
        private CachedPhpParser $cachedPhpParser
    ) {
    }

    /**
     * @param string[] $paths
     * @return string[]
     */
    public function resolve(array $paths, ?callable $progressClosure = null): array
    {
        Assert::allString($paths);
        Assert::allFileExists($paths);

        $phpFileInfos = PhpFilesFinder::find($paths);
        $mockedClassNameCollectingNodeVisitor = new MockedClassNameCollectingNodeVisitor();

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($mockedClassNameCollectingNodeVisitor);
        $this->traverseFileInfos($phpFileInfos, $nodeTraverser, $progressClosure);

        $mockedClassNames = $mockedClassNameCollectingNodeVisitor->getMockedClassNames();
        sort($mockedClassNames);

        return array_unique($mockedClassNames);
    }

    /**
     * @param SplFileInfo[] $phpFileInfos
     */
    private function traverseFileInfos(
        array $phpFileInfos,
        NodeTraverser $nodeTraverser,
        ?callable $progressClosure = null
    ): void {
        foreach ($phpFileInfos as $phpFileInfo) {
            $stmts = $this->cachedPhpParser->parseFile($phpFileInfo->getRealPath());

            $nodeTraverser->traverse($stmts);

            if (is_callable($progressClosure)) {
                $progressClosure();
            }
        }
    }
}

<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Lines;

use PhpParser\NodeTraverser;
use PhpParser\Parser;
use Rector\SwissKnife\Lines\NodeVisitor\CommentLineCountingNodeVisitor;
use Rector\SwissKnife\Lines\NodeVisitor\StructureNodeVisitor;
use Throwable;
use Webmozart\Assert\Assert;

/**
 * @see \Rector\SwissKnife\Tests\Lines\AnalyserTest
 */
final readonly class Analyser
{
    public function __construct(
        private Parser $parser,
    ) {
    }

    /**
     * @param string[] $filePaths
     */
    public function measureFiles(array $filePaths, ?callable $progressBarClosure = null): Measurements
    {
        $measurements = new Measurements();

        Assert::allString($filePaths);
        Assert::allFileExists($filePaths);

        foreach ($filePaths as $filePath) {
            $this->measureFile($measurements, $filePath);

            if (is_callable($progressBarClosure)) {
                $progressBarClosure();
            }
        }

        return $measurements;
    }

    private function measureFile(Measurements $measurements, string $filePath): void
    {
        Assert::fileExists($filePath);

        $fileContents = file_get_contents($filePath);
        Assert::string($fileContents);

        try {
            // avoid stop on invalid file contents
            $stmts = $this->parser->parse($fileContents);
        } catch (Throwable) {
            return;
        }

        if (! is_array($stmts)) {
            return;
        }

        $measurements->addFile($filePath);

        // measure structure and comment lines in a single traversal
        $commentLineCountingNodeVisitor = new CommentLineCountingNodeVisitor();

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new StructureNodeVisitor($measurements));
        $nodeTraverser->addVisitor($commentLineCountingNodeVisitor);
        $nodeTraverser->traverse($stmts);

        $measurements->incrementLines($this->resolveInitLinesOfCode($fileContents));
        $measurements->incrementCommentLines($commentLineCountingNodeVisitor->getCommentLineCount());
    }

    /**
     * @return int<0, max>
     */
    private function resolveInitLinesOfCode(string $fileContents): int
    {
        $linesOfCode = substr_count($fileContents, "\n");
        if ($linesOfCode === 0 && $fileContents !== '') {
            return 1;
        }

        return $linesOfCode;
    }
}

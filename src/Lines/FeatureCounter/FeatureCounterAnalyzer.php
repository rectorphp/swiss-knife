<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Lines\FeatureCounter;

use Entropy\Console\Output\ProgressBar;
use Entropy\FileSystem\FileInfo;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Rector\SwissKnife\Exception\ShouldNotHappenException;
use Rector\SwissKnife\Lines\FeatureCounter\NodeVisitor\FeatureCollectorNodeVisitor;
use Rector\SwissKnife\Lines\FeatureCounter\ValueObject\FeatureCollector;

/**
 * @see \Rector\SwissKnife\Tests\Lines\FeatureCounter\FeatureCounterAnalyzerTest
 */
final class FeatureCounterAnalyzer
{
    /**
     * @readonly
     */
    private FeatureCollector $featureCollector;

    /**
     * @readonly
     */
    private Parser $parser;

    public function __construct(
        FeatureCollector $featureCollector
    ) {
        $this->featureCollector = $featureCollector;
        $parserFactory = new ParserFactory();
        $this->parser = $parserFactory->createForNewestSupportedVersion();
    }

    /**
     * @param FileInfo[] $fileInfos
     */
    public function analyze(array $fileInfos): FeatureCollector
    {
        $progressBar = new ProgressBar();
        $progressBar->start(count($fileInfos));

        $featureCollectorNodeVisitor = new FeatureCollectorNodeVisitor($this->featureCollector);
        $nodeTraverser = new NodeTraverser($featureCollectorNodeVisitor);

        foreach ($fileInfos as $fileInfo) {
            $stmts = $this->parser->parse($fileInfo->getContents());
            if ($stmts === null) {
                throw new ShouldNotHappenException(sprintf(
                    'Parsing of file "%s" resulted in null statements.',
                    $fileInfo->getRealPath()
                ));
            }

            $nodeTraverser->traverse($stmts);

            $progressBar->advance();
        }

        $progressBar->finish();

        return $this->featureCollector;
    }
}

<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Lines\FeatureCounter\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Rector\SwissKnife\Lines\FeatureCounter\ValueObject\FeatureCollector;

final class FeatureCollectorNodeVisitor extends NodeVisitorAbstract
{
    public function __construct(
        private readonly FeatureCollector $featureCollector
    ) {
    }

    public function enterNode(Node $node): ?Node
    {
        foreach ($this->featureCollector->getPhpFeatures() as $phpFeature) {
            $callableNodeTrigger = $phpFeature->getNodeTrigger();
            if ($callableNodeTrigger($node)) {
                $phpFeature->increaseCount();
            }
        }

        return null;
    }
}

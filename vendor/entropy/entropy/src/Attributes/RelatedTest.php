<?php

declare (strict_types=1);
namespace SwissKnife202601\Entropy\Attributes;

use Attribute;
use SwissKnife202601\PHPUnit\Framework\TestCase;
#[Attribute(Attribute::TARGET_CLASS)]
final class RelatedTest
{
    /**
     * @param class-string<TestCase> $testClass
     */
    public function __construct(string $testClass)
    {
    }
}

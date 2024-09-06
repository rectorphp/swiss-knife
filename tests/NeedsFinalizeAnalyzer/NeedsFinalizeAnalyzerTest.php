<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\NeedsFinalizeAnalyzer;

use PHPUnit\Framework\Attributes\DataProvider;
use Rector\SwissKnife\Analyzer\NeedsFinalizeAnalyzer;
use Rector\SwissKnife\PhpParser\CachedPhpParser;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class NeedsFinalizeAnalyzerTest extends AbstractTestCase
{
    private NeedsFinalizeAnalyzer $needsFinalizeAnalyzer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->needsFinalizeAnalyzer = new NeedsFinalizeAnalyzer(
            excludedClasses: ['Rector\SwissKnife\Tests\NeedsFinalizeAnalyzer\Fixture\ExcludedClass'],
            cachedPhpParser: $this->make(CachedPhpParser::class),
        );
    }

    #[DataProvider('provideData')]
    public function test(string $filePath, bool $expected): void
    {
        $this->assertSame($expected, $this->needsFinalizeAnalyzer->isNeeded($filePath));
    }

    /**
     * @return iterable<array{string, bool}>
     */
    public static function provideData(): iterable
    {
        yield [__DIR__ . '/Fixture/excluded_class.php.inc', false];
        yield [__DIR__ . '/Fixture/final_class.php.inc', false];
        yield [__DIR__ . '/Fixture/abstract_class.php.inc', false];
        yield [__DIR__ . '/Fixture/anonymous_class.php.inc', false];

        yield [__DIR__ . '/Fixture/non_final_class.php.inc', true];
        yield [__DIR__ . '/Fixture/non_final_readonly_class.php.inc', true];
    }
}

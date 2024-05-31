<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Resolver\StaticClassConstResolver;

use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\Resolver\StaticClassConstResolver;
use Rector\SwissKnife\Tests\Resolver\StaticClassConstResolver\Fixture\FileWithStaticConstCalls;
use Symfony\Component\Finder\SplFileInfo;

final class StaticClassConstResolverTest extends TestCase
{
    private StaticClassConstResolver $staticClassConstResolver;

    protected function setUp(): void
    {
        $this->staticClassConstResolver = new StaticClassConstResolver();
    }

    public function test(): void
    {
        $splFileInfo = new SplFileInfo(__DIR__ . '/Fixture/FileWithStaticConstCalls.php', '', '');

        $classConstMatches = $this->staticClassConstResolver->resolve([$splFileInfo]);
        $this->assertCount(2, $classConstMatches);

        $firstClassConstMatch = $classConstMatches[0];
        $this->assertSame(FileWithStaticConstCalls::class, $firstClassConstMatch->getClassName());
        $this->assertSame('ITEM_NAME', $firstClassConstMatch->getConstantName());

        $secondClassConstMatch = $classConstMatches[1];
        $this->assertSame(FileWithStaticConstCalls::class, $secondClassConstMatch->getClassName());
        $this->assertSame('ITEM_PRICE', $secondClassConstMatch->getConstantName());
    }
}

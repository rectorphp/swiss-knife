<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Lines;

use Rector\SwissKnife\Lines\Analyser;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class AnalyserTest extends AbstractTestCase
{
    private Analyser $analyser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->analyser = $this->make(Analyser::class);
    }

    public function test(): void
    {
        $measurements = $this->analyser->measureFiles([__DIR__ . '/Fixture/source.php']);

        $this->assertSame(0, $measurements->getDirectoryCount());
        $this->assertSame(1, $measurements->getFileCount());

        // lines
        $this->assertSame(82, $measurements->getLines());
        $this->assertSame(75, $measurements->getNonCommentLines());
        $this->assertSame(7, $measurements->getCommentLines());

        // structure
        $this->assertSame(1, $measurements->getNamespaceCount());
        $this->assertSame(2, $measurements->getClassCount());
        $this->assertSame(4, $measurements->getMethodCount());
        $this->assertSame(1, $measurements->getClassConstantCount());
        $this->assertSame(1, $measurements->getInterfaceCount());
        $this->assertSame(0, $measurements->getTraitCount());
        $this->assertSame(1, $measurements->getFunctionCount());
        $this->assertSame(1, $measurements->getClosureCount());
        $this->assertSame(1, $measurements->getGlobalConstantCount());

        // methods
        $this->assertSame(2, $measurements->getPublicMethods());
        $this->assertSame(1, $measurements->getProtectedMethods());
        $this->assertSame(1, $measurements->getPrivateMethods());

        // static and non-static
        $this->assertSame(1, $measurements->getStaticMethods());
        $this->assertSame(3, $measurements->getNonStaticMethods());
        $this->assertEqualsWithDelta(25.0, $measurements->getStaticMethodsRelative(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(75.0, $measurements->getNonStaticMethodsRelative(), PHP_FLOAT_EPSILON);
    }
}

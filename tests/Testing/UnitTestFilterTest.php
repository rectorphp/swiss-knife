<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Testing;

use PHPUnit\Framework\TestCase;
use Rector\SwissKnife\Testing\UnitTestFilter;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class UnitTestFilterTest extends AbstractTestCase
{
    public function testFilter(): void
    {
        $unitTestFilter = $this->make(UnitTestFilter::class);

        $testClassesToFilePaths = [
            'Rector\SwissKnife\Tests\AbstractTestCase' => __DIR__ . '/../AbstractTestCase.php',
            'Symfony\Bundle\FrameworkBundle\Test\KernelTestCase' => '/some/path/KernelTestCase.php',
            'NotATestClass' => '/some/path/NotATestClass.php',
        ];

        $filtered = $unitTestFilter->filter($testClassesToFilePaths);

        $this->assertSame(
            ['Rector\SwissKnife\Tests\AbstractTestCase' => __DIR__ . '/../AbstractTestCase.php'],
            $filtered
        );
    }
}

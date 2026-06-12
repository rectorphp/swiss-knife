<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Testing;

use Rector\SwissKnife\Testing\UnitTestFilter;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class UnitTestFilterExtendedTest extends AbstractTestCase
{
    public function testFilterFormTypeTestCase(): void
    {
        $unitTestFilter = $this->make(UnitTestFilter::class);

        $testClassesToFilePaths = [
            'Symfony\Component\Form\Test\TypeTestCase' => '/some/path/TypeTestCase.php',
            'Rector\SwissKnife\Tests\AbstractTestCase' => __DIR__ . '/../AbstractTestCase.php',
        ];

        $filtered = $unitTestFilter->filter($testClassesToFilePaths);

        $this->assertSame(
            ['Rector\SwissKnife\Tests\AbstractTestCase' => __DIR__ . '/../AbstractTestCase.php'],
            $filtered
        );
    }
}

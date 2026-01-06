<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Testing;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class UnitTestFilter
{
    /**
     * @var string[]|class-string<KernelTestCase>[]
     */
    private const array NON_UNIT_TEST_CASE_CLASSES = [
        KernelTestCase::class,
        'Symfony\Component\Form\Test\TypeTestCase',
    ];

    /**
     * @param array<string, string> $testClassesToFilePaths
     * @return array<string, string>
     */
    public function filter(array $testClassesToFilePaths): array
    {
        return array_filter($testClassesToFilePaths, $this->isUnitTest(...), ARRAY_FILTER_USE_KEY);
    }

    private function isUnitTest(string $class): bool
    {
        if (! is_a($class, 'PHPUnit\Framework\TestCase', true) && ! is_a($class, 'PHPUnit_Framework_TestCase', true)) {
            return false;
        }

        foreach (self::NON_UNIT_TEST_CASE_CLASSES as $nonUnitTestCaseClass) {
            // required special behavior
            if (is_a($class, $nonUnitTestCaseClass, true)) {
                return false;
            }
        }

        return true;
    }
}

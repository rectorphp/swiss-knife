<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Testing;

use EasyCI20220202\PHPUnit\Framework\TestCase;
use EasyCI20220202\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
final class UnitTestFilter
{
    /**
     * @var string[]|class-string<KernelTestCase>[]
     */
    private const NON_UNIT_TEST_CASE_CLASSES = [\EasyCI20220202\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase::class, 'EasyCI20220202\\Symfony\\Component\\Form\\Test\\TypeTestCase'];
    /**
     * @param array<string, string> $testClassesToFilePaths
     * @return array<string, string>
     */
    public function filter(array $testClassesToFilePaths) : array
    {
        return \array_filter($testClassesToFilePaths, function (string $testClass) : bool {
            return $this->isUnitTest($testClass);
        }, \ARRAY_FILTER_USE_KEY);
    }
    private function isUnitTest(string $class) : bool
    {
        if (!\is_a($class, \EasyCI20220202\PHPUnit\Framework\TestCase::class, \true)) {
            return \false;
        }
        foreach (self::NON_UNIT_TEST_CASE_CLASSES as $nonUnitTestCaseClass) {
            // required special behavior
            if (\is_a($class, $nonUnitTestCaseClass, \true)) {
                return \false;
            }
        }
        return \true;
    }
}

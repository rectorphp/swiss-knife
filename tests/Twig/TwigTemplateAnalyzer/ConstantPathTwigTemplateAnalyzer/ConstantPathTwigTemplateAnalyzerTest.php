<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Twig\TwigTemplateAnalyzer\ConstantPathTwigTemplateAnalyzer;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\EasyCI\Kernel\EasyCIKernel;
use Symplify\EasyCI\Twig\TwigTemplateAnalyzer\ConstantPathTwigTemplateAnalyzer;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ConstantPathTwigTemplateAnalyzerTest extends AbstractKernelTestCase
{
    private ConstantPathTwigTemplateAnalyzer $constantPathTwigTemplateAnalyzer;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->constantPathTwigTemplateAnalyzer = $this->getService(ConstantPathTwigTemplateAnalyzer::class);
    }

    #[DataProvider('provideData')]
    public function test(SmartFileInfo $inputFileInfo, int $expectedErrorCount): void
    {
        $templateErrors = $this->constantPathTwigTemplateAnalyzer->analyze([$inputFileInfo]);
        $this->assertCount($expectedErrorCount, $templateErrors);
    }

    /**
     * @return Iterator<int[]|SmartFileInfo[]>
     */
    public static function provideData(): Iterator
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/path_with_constant.twig'), 0];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/path_with_string.twig'), 1];
    }
}

<?php

declare(strict_types=1);

namespace Migrify\EasyCI\Tests\Sonar\SonarConfigGenerator;

use Iterator;
use Migrify\EasyCI\HttpKernel\EasyCIKernel;
use Migrify\EasyCI\Sonar\SonarConfigGenerator;
use Migrify\EasyCI\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class SonarConfigGeneratorTest extends AbstractKernelTestCase
{
    /**
     * @var SonarConfigGenerator
     */
    private $sonarConfigGenerator;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->sonarConfigGenerator = self::$container->get(SonarConfigGenerator::class);

        /** @var ParameterProvider $parameterProvider */
        $parameterProvider = self::$container->get(ParameterProvider::class);
        $parameterProvider->changeParameter(Option::SONAR_ORGANIZATION, 'some_organization');
        $parameterProvider->changeParameter(Option::SONAR_PROJECT_KEY, 'some_project');
    }

    /**
     * @param array<string, mixed|mixed[]> $extraParameters
     * @dataProvider provideData()
     */
    public function test(array $extraParameters, string $expectedSonartConfig): void
    {
        $sonarConfigContent = $this->sonarConfigGenerator->generate([__DIR__ . '/Fixture'], $extraParameters);
        $this->assertStringEqualsFile($expectedSonartConfig, $sonarConfigContent);
    }

    public function provideData(): Iterator
    {
        yield [[], __DIR__ . '/Fixture/expected_config.txt'];
        yield [['sonar.extra' => 'extra_values'], __DIR__ . '/Fixture/expected_modified_original_config.txt'];
    }
}

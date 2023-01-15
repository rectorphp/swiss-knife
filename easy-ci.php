<?php

declare(strict_types=1);

use Symplify\EasyCI\Config\Contract\ConfigFileAnalyzerInterface;
use Symplify\EasyCI\Config\EasyCIConfig;
use Symplify\EasyCI\Twig\TwigTemplateAnalyzer\ConstantPathTwigTemplateAnalyzer;
use Symplify\EasyCI\Twig\TwigTemplateAnalyzer\MissingClassConstantTwigAnalyzer;

return static function (EasyCIConfig $easyCIConfig): void {
    $easyCIConfig->paths([__DIR__ . '/src', __DIR__ . '/packages', __DIR__ . '/config']);

    $easyCIConfig->typesToSkip([
        ConstantPathTwigTemplateAnalyzer::class,
        MissingClassConstantTwigAnalyzer::class,
        ConfigFileAnalyzerInterface::class,
    ]);
};

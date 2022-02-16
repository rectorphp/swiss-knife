<?php

declare (strict_types=1);
namespace EasyCI20220216;

use EasyCI20220216\PhpParser\ConstExprEvaluator;
use EasyCI20220216\PhpParser\NodeFinder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use EasyCI20220216\Symplify\Astral\PhpParser\SmartPhpParser;
use EasyCI20220216\Symplify\Astral\PhpParser\SmartPhpParserFactory;
use EasyCI20220216\Symplify\PackageBuilder\Php\TypeChecker;
use function EasyCI20220216\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->autowire()->autoconfigure()->public();
    $services->load('EasyCI20220216\Symplify\Astral\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/StaticFactory', __DIR__ . '/../src/ValueObject', __DIR__ . '/../src/NodeVisitor', __DIR__ . '/../src/PhpParser/SmartPhpParser.php']);
    $services->set(\EasyCI20220216\Symplify\Astral\PhpParser\SmartPhpParser::class)->factory([\EasyCI20220216\Symfony\Component\DependencyInjection\Loader\Configurator\service(\EasyCI20220216\Symplify\Astral\PhpParser\SmartPhpParserFactory::class), 'create']);
    $services->set(\EasyCI20220216\PhpParser\ConstExprEvaluator::class);
    $services->set(\EasyCI20220216\Symplify\PackageBuilder\Php\TypeChecker::class);
    $services->set(\EasyCI20220216\PhpParser\NodeFinder::class);
};

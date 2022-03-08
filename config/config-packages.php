<?php

declare (strict_types=1);
namespace EasyCI20220308;

use EasyCI20220308\PhpParser\Parser;
use EasyCI20220308\PhpParser\ParserFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCI\StaticDetector\NodeTraverser\StaticCollectNodeTraverser;
use Symplify\EasyCI\StaticDetector\NodeTraverser\StaticCollectNodeTraverserFactory;
use Symplify\EasyCI\ValueObject\Option;
use EasyCI20220308\Symplify\PackageBuilder\Parameter\ParameterProvider;
use function EasyCI20220308\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(\Symplify\EasyCI\ValueObject\Option::TYPES_TO_SKIP, []);
    $parameters->set(\Symplify\EasyCI\ValueObject\Option::EXCLUDED_CHECK_PATHS, []);
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('Symplify\\EasyCI\\', __DIR__ . '/../packages')->exclude([__DIR__ . '/../packages/StaticDetector/ValueObject', __DIR__ . '/../packages/ActiveClass/ValueObject', __DIR__ . '/../packages/Psr4/ValueObject']);
    $services->set(\Symplify\EasyCI\StaticDetector\NodeTraverser\StaticCollectNodeTraverser::class)->factory([\EasyCI20220308\Symfony\Component\DependencyInjection\Loader\Configurator\service(\Symplify\EasyCI\StaticDetector\NodeTraverser\StaticCollectNodeTraverserFactory::class), 'create']);
    $services->set(\EasyCI20220308\PhpParser\ParserFactory::class);
    $services->set(\EasyCI20220308\PhpParser\Parser::class)->factory([\EasyCI20220308\Symfony\Component\DependencyInjection\Loader\Configurator\service(\EasyCI20220308\PhpParser\ParserFactory::class), 'create'])->arg('$kind', \EasyCI20220308\PhpParser\ParserFactory::PREFER_PHP7);
    $services->set(\EasyCI20220308\Symplify\PackageBuilder\Parameter\ParameterProvider::class)->args([\EasyCI20220308\Symfony\Component\DependencyInjection\Loader\Configurator\service('service_container')]);
};

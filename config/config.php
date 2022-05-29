<?php

declare (strict_types=1);
namespace EasyCI20220529;

use EasyCI20220529\Composer\Semver\Semver;
use EasyCI20220529\Composer\Semver\VersionParser;
use EasyCI20220529\Nette\Neon\Decoder;
use EasyCI20220529\PhpParser\NodeFinder;
use EasyCI20220529\PhpParser\Parser;
use EasyCI20220529\PhpParser\ParserFactory;
use EasyCI20220529\PhpParser\PrettyPrinter\Standard;
use EasyCI20220529\Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCI\Console\EasyCIApplication;
use Symplify\EasyCI\StaticDetector\NodeTraverser\StaticCollectNodeTraverser;
use Symplify\EasyCI\StaticDetector\NodeTraverser\StaticCollectNodeTraverserFactory;
use Symplify\EasyCI\ValueObject\Option;
use EasyCI20220529\Symplify\PackageBuilder\Parameter\ParameterProvider;
use EasyCI20220529\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use function EasyCI20220529\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('Symplify\\EasyCI\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/Kernel', __DIR__ . '/../src/ValueObject', __DIR__ . '/../src/Config/EasyCIConfig.php']);
    $services->load('Symplify\\EasyCI\\', __DIR__ . '/../packages')->exclude([__DIR__ . '/../packages/StaticDetector/ValueObject', __DIR__ . '/../packages/ActiveClass/ValueObject', __DIR__ . '/../packages/Psr4/ValueObject']);
    // for autowired commands
    $services->alias(\EasyCI20220529\Symfony\Component\Console\Application::class, \Symplify\EasyCI\Console\EasyCIApplication::class);
    $services->set(\EasyCI20220529\Composer\Semver\VersionParser::class);
    $services->set(\EasyCI20220529\Composer\Semver\Semver::class);
    // neon
    $services->set(\EasyCI20220529\Nette\Neon\Decoder::class);
    // php-parser
    $services->set(\EasyCI20220529\PhpParser\ParserFactory::class);
    $services->set(\EasyCI20220529\PhpParser\Parser::class)->factory([\EasyCI20220529\Symfony\Component\DependencyInjection\Loader\Configurator\service(\EasyCI20220529\PhpParser\ParserFactory::class), 'create'])->args([\EasyCI20220529\PhpParser\ParserFactory::PREFER_PHP7]);
    $services->set(\EasyCI20220529\PhpParser\PrettyPrinter\Standard::class);
    $services->set(\EasyCI20220529\PhpParser\NodeFinder::class);
    $services->set(\EasyCI20220529\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker::class);
    $parameters = $containerConfigurator->parameters();
    $parameters->set(\Symplify\EasyCI\ValueObject\Option::TYPES_TO_SKIP, []);
    $parameters->set(\Symplify\EasyCI\ValueObject\Option::EXCLUDED_CHECK_PATHS, []);
    $services->set(\Symplify\EasyCI\StaticDetector\NodeTraverser\StaticCollectNodeTraverser::class)->factory([\EasyCI20220529\Symfony\Component\DependencyInjection\Loader\Configurator\service(\Symplify\EasyCI\StaticDetector\NodeTraverser\StaticCollectNodeTraverserFactory::class), 'create']);
    $services->set(\EasyCI20220529\PhpParser\ParserFactory::class);
    $services->set(\EasyCI20220529\PhpParser\Parser::class)->factory([\EasyCI20220529\Symfony\Component\DependencyInjection\Loader\Configurator\service(\EasyCI20220529\PhpParser\ParserFactory::class), 'create'])->arg('$kind', \EasyCI20220529\PhpParser\ParserFactory::PREFER_PHP7);
    $services->set(\EasyCI20220529\Symplify\PackageBuilder\Parameter\ParameterProvider::class)->args([\EasyCI20220529\Symfony\Component\DependencyInjection\Loader\Configurator\service('service_container')]);
};

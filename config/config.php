<?php

declare (strict_types=1);
namespace EasyCI20220607;

use EasyCI20220607\Composer\Semver\Semver;
use EasyCI20220607\Composer\Semver\VersionParser;
use EasyCI20220607\Nette\Neon\Decoder;
use EasyCI20220607\PhpParser\NodeFinder;
use EasyCI20220607\PhpParser\Parser;
use EasyCI20220607\PhpParser\ParserFactory;
use EasyCI20220607\PhpParser\PrettyPrinter\Standard;
use EasyCI20220607\Symfony\Component\Console\Application;
use EasyCI20220607\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use EasyCI20220607\Symplify\EasyCI\Console\EasyCIApplication;
use EasyCI20220607\Symplify\EasyCI\StaticDetector\NodeTraverser\StaticCollectNodeTraverser;
use EasyCI20220607\Symplify\EasyCI\StaticDetector\NodeTraverser\StaticCollectNodeTraverserFactory;
use EasyCI20220607\Symplify\EasyCI\ValueObject\Option;
use EasyCI20220607\Symplify\PackageBuilder\Parameter\ParameterProvider;
use EasyCI20220607\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use function EasyCI20220607\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    $services->load('Symplify\\EasyCI\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/Kernel', __DIR__ . '/../src/ValueObject', __DIR__ . '/../src/Config/EasyCIConfig.php']);
    $services->load('Symplify\\EasyCI\\', __DIR__ . '/../packages')->exclude([__DIR__ . '/../packages/StaticDetector/ValueObject', __DIR__ . '/../packages/ActiveClass/ValueObject', __DIR__ . '/../packages/Psr4/ValueObject']);
    // for autowired commands
    $services->alias(Application::class, EasyCIApplication::class);
    $services->set(VersionParser::class);
    $services->set(Semver::class);
    // neon
    $services->set(Decoder::class);
    // php-parser
    $services->set(ParserFactory::class);
    $services->set(Parser::class)->factory([service(ParserFactory::class), 'create'])->args([ParserFactory::PREFER_PHP7]);
    $services->set(Standard::class);
    $services->set(NodeFinder::class);
    $services->set(ClassLikeExistenceChecker::class);
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::TYPES_TO_SKIP, []);
    $parameters->set(Option::EXCLUDED_CHECK_PATHS, []);
    $services->set(StaticCollectNodeTraverser::class)->factory([service(StaticCollectNodeTraverserFactory::class), 'create']);
    $services->set(ParserFactory::class);
    $services->set(Parser::class)->factory([service(ParserFactory::class), 'create'])->arg('$kind', ParserFactory::PREFER_PHP7);
    $services->set(ParameterProvider::class)->args([service('service_container')]);
};

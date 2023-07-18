<?php

declare(strict_types=1);

use PhpParser\NodeFinder;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCI\Config\Application\ClassAndConstantExistanceFileProcessor;
use Symplify\EasyCI\Config\ConfigFileAnalyzer\NonExistingClassConfigFileAnalyzer;
use Symplify\EasyCI\Config\ConfigFileAnalyzer\NonExistingClassConstantConfigFileAnalyzer;
use Symplify\EasyCI\Config\Contract\ConfigFileAnalyzerInterface;
use Symplify\EasyCI\Console\EasyCIApplication;
use Symplify\EasyCI\Twig\Contract\TwigTemplateAnalyzerInterface;
use Symplify\EasyCI\Twig\TwigTemplateAnalyzer\ConstantPathTwigTemplateAnalyzer;
use Symplify\EasyCI\Twig\TwigTemplateAnalyzer\MissingClassConstantTwigAnalyzer;
use Symplify\EasyCI\Twig\TwigTemplateProcessor;
use Symplify\EasyCI\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire();

    $services->load('Symplify\EasyCI\\', __DIR__ . '/../src')
        ->exclude([
            __DIR__ . '/../src/Kernel',
            __DIR__ . '/../src/ValueObject',
            __DIR__ . '/../src/Config/EasyCIConfig.php',
        ]);

    $services->load('Symplify\EasyCI\\', __DIR__ . '/../packages')
        ->exclude([__DIR__ . '/../packages/Psr4/ValueObject']);

    // for autowired commands
    $services->alias(Application::class, EasyCIApplication::class);

    $services->set(Standard::class);
    $services->set(NodeFinder::class);
    $services->set(ClassLikeExistenceChecker::class);

    // tagged services
    $services->set(NonExistingClassConstantConfigFileAnalyzer::class)
        ->tag(ConfigFileAnalyzerInterface::class);
    $services->set(NonExistingClassConfigFileAnalyzer::class)
        ->tag(ConfigFileAnalyzerInterface::class);
    $services->set(ClassAndConstantExistanceFileProcessor::class)
        ->args([tagged_iterator(ConfigFileAnalyzerInterface::class)]);

    $services->set(ConstantPathTwigTemplateAnalyzer::class)
        ->tag(TwigTemplateAnalyzerInterface::class);
    $services->set(MissingClassConstantTwigAnalyzer::class)
        ->tag(TwigTemplateAnalyzerInterface::class);
    $services->set(TwigTemplateProcessor::class)
        ->args([tagged_iterator(TwigTemplateAnalyzerInterface::class)]);

    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::EXCLUDED_CHECK_PATHS, []);

    $services->set(ParserFactory::class);
    $services->set(Parser::class)
        ->factory([service(ParserFactory::class), 'create'])
        ->arg('$kind', ParserFactory::PREFER_PHP7);

    $services->set(ParameterProvider::class)
        ->args([service('service_container')]);
};

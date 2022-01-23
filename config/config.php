<?php

declare (strict_types=1);
namespace EasyCI20220123;

use EasyCI20220123\Composer\Semver\Semver;
use EasyCI20220123\Composer\Semver\VersionParser;
use EasyCI20220123\Nette\Neon\Decoder;
use EasyCI20220123\PhpParser\NodeFinder;
use EasyCI20220123\PhpParser\Parser;
use EasyCI20220123\PhpParser\ParserFactory;
use EasyCI20220123\PhpParser\PrettyPrinter\Standard;
use EasyCI20220123\Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCI\ActiveClass\Command\CheckActiveClassCommand;
use Symplify\EasyCI\Command\CheckCommentedCodeCommand;
use Symplify\EasyCI\Command\CheckConflictsCommand;
use Symplify\EasyCI\Command\CheckLatteTemplateCommand;
use Symplify\EasyCI\Command\CheckTwigRenderCommand;
use Symplify\EasyCI\Command\CheckTwigTemplateCommand;
use Symplify\EasyCI\Command\PhpVersionsJsonCommand;
use Symplify\EasyCI\Command\ValidateFileLengthCommand;
use Symplify\EasyCI\Config\Command\CheckConfigCommand;
use Symplify\EasyCI\Neon\Command\CheckNeonCommand;
use Symplify\EasyCI\Psr4\Command\CheckFileClassNameCommand;
use Symplify\EasyCI\Psr4\Command\FindMultiClassesCommand;
use Symplify\EasyCI\Psr4\Command\GeneratePsr4ToPathsCommand;
use Symplify\EasyCI\StaticDetector\Command\DetectStaticCommand;
use Symplify\EasyCI\Testing\Command\DetectUnitTestsCommand;
use EasyCI20220123\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use function EasyCI20220123\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $containerConfigurator->import(__DIR__ . '/config-packages.php');
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('Symplify\\EasyCI\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/Kernel', __DIR__ . '/../src/ValueObject']);
    // console
    $services->set(\EasyCI20220123\Symfony\Component\Console\Application::class)->call('addCommands', [[
        // basic commands
        \EasyCI20220123\Symfony\Component\DependencyInjection\Loader\Configurator\service(\Symplify\EasyCI\Command\CheckCommentedCodeCommand::class),
        \EasyCI20220123\Symfony\Component\DependencyInjection\Loader\Configurator\service(\Symplify\EasyCI\Command\CheckConflictsCommand::class),
        \EasyCI20220123\Symfony\Component\DependencyInjection\Loader\Configurator\service(\Symplify\EasyCI\Command\CheckLatteTemplateCommand::class),
        \EasyCI20220123\Symfony\Component\DependencyInjection\Loader\Configurator\service(\Symplify\EasyCI\Command\CheckTwigRenderCommand::class),
        \EasyCI20220123\Symfony\Component\DependencyInjection\Loader\Configurator\service(\Symplify\EasyCI\Command\CheckTwigTemplateCommand::class),
        \EasyCI20220123\Symfony\Component\DependencyInjection\Loader\Configurator\service(\Symplify\EasyCI\Command\PhpVersionsJsonCommand::class),
        \EasyCI20220123\Symfony\Component\DependencyInjection\Loader\Configurator\service(\Symplify\EasyCI\Command\ValidateFileLengthCommand::class),
        // package commands
        // active classes
        \EasyCI20220123\Symfony\Component\DependencyInjection\Loader\Configurator\service(\Symplify\EasyCI\ActiveClass\Command\CheckActiveClassCommand::class),
        // config
        \EasyCI20220123\Symfony\Component\DependencyInjection\Loader\Configurator\service(\Symplify\EasyCI\Config\Command\CheckConfigCommand::class),
        // neon
        \EasyCI20220123\Symfony\Component\DependencyInjection\Loader\Configurator\service(\Symplify\EasyCI\Neon\Command\CheckNeonCommand::class),
        // PSR-4
        \EasyCI20220123\Symfony\Component\DependencyInjection\Loader\Configurator\service(\Symplify\EasyCI\Psr4\Command\CheckFileClassNameCommand::class),
        \EasyCI20220123\Symfony\Component\DependencyInjection\Loader\Configurator\service(\Symplify\EasyCI\Psr4\Command\FindMultiClassesCommand::class),
        \EasyCI20220123\Symfony\Component\DependencyInjection\Loader\Configurator\service(\Symplify\EasyCI\Psr4\Command\GeneratePsr4ToPathsCommand::class),
        // static
        \EasyCI20220123\Symfony\Component\DependencyInjection\Loader\Configurator\service(\Symplify\EasyCI\StaticDetector\Command\DetectStaticCommand::class),
        // local unit test
        \EasyCI20220123\Symfony\Component\DependencyInjection\Loader\Configurator\service(\Symplify\EasyCI\Testing\Command\DetectUnitTestsCommand::class),
    ]]);
    $services->set(\EasyCI20220123\Composer\Semver\VersionParser::class);
    $services->set(\EasyCI20220123\Composer\Semver\Semver::class);
    // neon
    $services->set(\EasyCI20220123\Nette\Neon\Decoder::class);
    // php-parser
    $services->set(\EasyCI20220123\PhpParser\ParserFactory::class);
    $services->set(\EasyCI20220123\PhpParser\Parser::class)->factory([\EasyCI20220123\Symfony\Component\DependencyInjection\Loader\Configurator\service(\EasyCI20220123\PhpParser\ParserFactory::class), 'create'])->args([\EasyCI20220123\PhpParser\ParserFactory::PREFER_PHP7]);
    $services->set(\EasyCI20220123\PhpParser\PrettyPrinter\Standard::class);
    $services->set(\EasyCI20220123\PhpParser\NodeFinder::class);
    $services->set(\EasyCI20220123\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker::class);
};

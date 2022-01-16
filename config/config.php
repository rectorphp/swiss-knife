<?php

declare (strict_types=1);
namespace EasyCI20220116;

use EasyCI20220116\Composer\Semver\Semver;
use EasyCI20220116\Composer\Semver\VersionParser;
use EasyCI20220116\Nette\Neon\Decoder;
use EasyCI20220116\PhpParser\NodeFinder;
use EasyCI20220116\PhpParser\Parser;
use EasyCI20220116\PhpParser\ParserFactory;
use EasyCI20220116\PhpParser\PrettyPrinter\Standard;
use EasyCI20220116\Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use EasyCI20220116\Symplify\EasyCI\ActiveClass\Command\CheckActiveClassCommand;
use EasyCI20220116\Symplify\EasyCI\Command\CheckCommentedCodeCommand;
use EasyCI20220116\Symplify\EasyCI\Command\CheckConflictsCommand;
use EasyCI20220116\Symplify\EasyCI\Command\CheckLatteTemplateCommand;
use EasyCI20220116\Symplify\EasyCI\Command\CheckTwigRenderCommand;
use EasyCI20220116\Symplify\EasyCI\Command\CheckTwigTemplateCommand;
use EasyCI20220116\Symplify\EasyCI\Command\PhpVersionsJsonCommand;
use EasyCI20220116\Symplify\EasyCI\Command\ValidateFileLengthCommand;
use EasyCI20220116\Symplify\EasyCI\Config\Command\CheckConfigCommand;
use EasyCI20220116\Symplify\EasyCI\Neon\Command\CheckNeonCommand;
use EasyCI20220116\Symplify\EasyCI\Psr4\Command\CheckFileClassNameCommand;
use EasyCI20220116\Symplify\EasyCI\Psr4\Command\FindMultiClassesCommand;
use EasyCI20220116\Symplify\EasyCI\Psr4\Command\GeneratePsr4ToPathsCommand;
use EasyCI20220116\Symplify\EasyCI\StaticDetector\Command\DetectStaticCommand;
use EasyCI20220116\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use function EasyCI20220116\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $containerConfigurator->import(__DIR__ . '/config-packages.php');
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('EasyCI20220116\Symplify\EasyCI\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/Kernel', __DIR__ . '/../src/ValueObject']);
    // console
    $services->set(\EasyCI20220116\Symfony\Component\Console\Application::class)->call('addCommands', [[
        // basic commands
        \EasyCI20220116\Symfony\Component\DependencyInjection\Loader\Configurator\service(\EasyCI20220116\Symplify\EasyCI\Command\CheckCommentedCodeCommand::class),
        \EasyCI20220116\Symfony\Component\DependencyInjection\Loader\Configurator\service(\EasyCI20220116\Symplify\EasyCI\Command\CheckConflictsCommand::class),
        \EasyCI20220116\Symfony\Component\DependencyInjection\Loader\Configurator\service(\EasyCI20220116\Symplify\EasyCI\Command\CheckLatteTemplateCommand::class),
        \EasyCI20220116\Symfony\Component\DependencyInjection\Loader\Configurator\service(\EasyCI20220116\Symplify\EasyCI\Command\CheckTwigRenderCommand::class),
        \EasyCI20220116\Symfony\Component\DependencyInjection\Loader\Configurator\service(\EasyCI20220116\Symplify\EasyCI\Command\CheckTwigTemplateCommand::class),
        \EasyCI20220116\Symfony\Component\DependencyInjection\Loader\Configurator\service(\EasyCI20220116\Symplify\EasyCI\Command\PhpVersionsJsonCommand::class),
        \EasyCI20220116\Symfony\Component\DependencyInjection\Loader\Configurator\service(\EasyCI20220116\Symplify\EasyCI\Command\ValidateFileLengthCommand::class),
        // package commands
        \EasyCI20220116\Symfony\Component\DependencyInjection\Loader\Configurator\service(\EasyCI20220116\Symplify\EasyCI\ActiveClass\Command\CheckActiveClassCommand::class),
        \EasyCI20220116\Symfony\Component\DependencyInjection\Loader\Configurator\service(\EasyCI20220116\Symplify\EasyCI\Config\Command\CheckConfigCommand::class),
        \EasyCI20220116\Symfony\Component\DependencyInjection\Loader\Configurator\service(\EasyCI20220116\Symplify\EasyCI\Neon\Command\CheckNeonCommand::class),
        \EasyCI20220116\Symfony\Component\DependencyInjection\Loader\Configurator\service(\EasyCI20220116\Symplify\EasyCI\Psr4\Command\CheckFileClassNameCommand::class),
        \EasyCI20220116\Symfony\Component\DependencyInjection\Loader\Configurator\service(\EasyCI20220116\Symplify\EasyCI\Psr4\Command\FindMultiClassesCommand::class),
        \EasyCI20220116\Symfony\Component\DependencyInjection\Loader\Configurator\service(\EasyCI20220116\Symplify\EasyCI\Psr4\Command\GeneratePsr4ToPathsCommand::class),
        \EasyCI20220116\Symfony\Component\DependencyInjection\Loader\Configurator\service(\EasyCI20220116\Symplify\EasyCI\StaticDetector\Command\DetectStaticCommand::class),
    ]]);
    $services->set(\EasyCI20220116\Composer\Semver\VersionParser::class);
    $services->set(\EasyCI20220116\Composer\Semver\Semver::class);
    // neon
    $services->set(\EasyCI20220116\Nette\Neon\Decoder::class);
    // php-parser
    $services->set(\EasyCI20220116\PhpParser\ParserFactory::class);
    $services->set(\EasyCI20220116\PhpParser\Parser::class)->factory([\EasyCI20220116\Symfony\Component\DependencyInjection\Loader\Configurator\service(\EasyCI20220116\PhpParser\ParserFactory::class), 'create'])->args([\EasyCI20220116\PhpParser\ParserFactory::PREFER_PHP7]);
    $services->set(\EasyCI20220116\PhpParser\PrettyPrinter\Standard::class);
    $services->set(\EasyCI20220116\PhpParser\NodeFinder::class);
    $services->set(\EasyCI20220116\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker::class);
};

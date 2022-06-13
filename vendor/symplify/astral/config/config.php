<?php

declare (strict_types=1);
namespace EasyCI202206;

use EasyCI202206\PhpParser\ConstExprEvaluator;
use EasyCI202206\PhpParser\NodeFinder;
use EasyCI202206\PHPStan\PhpDocParser\Lexer\Lexer;
use EasyCI202206\PHPStan\PhpDocParser\Parser\ConstExprParser;
use EasyCI202206\PHPStan\PhpDocParser\Parser\PhpDocParser;
use EasyCI202206\PHPStan\PhpDocParser\Parser\TypeParser;
use EasyCI202206\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use EasyCI202206\Symplify\Astral\PhpParser\SmartPhpParser;
use EasyCI202206\Symplify\Astral\PhpParser\SmartPhpParserFactory;
use EasyCI202206\Symplify\PackageBuilder\Php\TypeChecker;
use function EasyCI202206\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->autowire()->public();
    $services->load('EasyCI202206\Symplify\Astral\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/StaticFactory', __DIR__ . '/../src/ValueObject', __DIR__ . '/../src/NodeVisitor', __DIR__ . '/../src/PhpParser/SmartPhpParser.php', __DIR__ . '/../src/PhpDocParser/PhpDocNodeVisitor/CallablePhpDocNodeVisitor.php']);
    $services->set(SmartPhpParser::class)->factory([service(SmartPhpParserFactory::class), 'create']);
    $services->set(ConstExprEvaluator::class);
    $services->set(TypeChecker::class);
    $services->set(NodeFinder::class);
    // phpdoc parser
    $services->set(PhpDocParser::class);
    $services->set(Lexer::class);
    $services->set(TypeParser::class);
    $services->set(ConstExprParser::class);
};

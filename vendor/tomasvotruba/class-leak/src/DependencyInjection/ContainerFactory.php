<?php

declare (strict_types=1);
namespace SwissKnife202606\TomasVotruba\ClassLeak\DependencyInjection;

use SwissKnife202606\Entropy\Container\Container;
use SwissKnife202606\PhpParser\Parser;
use SwissKnife202606\PhpParser\ParserFactory;
/**
 * @api
 */
final class ContainerFactory
{
    /**
     * @api
     */
    public function create() : Container
    {
        $container = new Container();
        $container->autodiscover(__DIR__ . '/..');
        $container->service(Parser::class, static function () : Parser {
            $parserFactory = new ParserFactory();
            return $parserFactory->createForHostVersion();
        });
        return $container;
    }
}

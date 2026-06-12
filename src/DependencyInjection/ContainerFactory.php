<?php

declare(strict_types=1);

namespace Rector\SwissKnife\DependencyInjection;

use Entropy\Container\Container;
use PhpParser\Parser;
use PhpParser\ParserFactory;

/**
 * @api used in tests
 */
final class ContainerFactory
{
    public function create(): Container
    {
        $container = new Container();

        $container->autodiscover(__DIR__ . '/../Command');

        $container->service(Parser::class, static function (): Parser {
            $phpParserFactory = new ParserFactory();
            return $phpParserFactory->createForNewestSupportedVersion();
        });

        return $container;
    }
}

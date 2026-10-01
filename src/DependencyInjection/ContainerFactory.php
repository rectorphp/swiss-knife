<?php

declare(strict_types=1);

namespace Rector\SwissKnife\DependencyInjection;

use Entropy\Container\Container;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Rector\SwissKnife\Jack\Composer\InstalledVersionResolver;

/**
 * @api used in tests
 */
final class ContainerFactory
{
    public function create(): Container
    {
        $container = new Container();

        $container->autodiscover(__DIR__ . '/../Command');
        $container->autodiscover(__DIR__ . '/../Jack/Command');

        $container->service(Parser::class, static function (): Parser {
            $phpParserFactory = new ParserFactory();
            return $phpParserFactory->createForNewestSupportedVersion();
        });

        // resolve installed.json from the current working directory of the analyzed project
        $container->service(
            InstalledVersionResolver::class,
            static fn (): InstalledVersionResolver => new InstalledVersionResolver(
                getcwd() . '/vendor/composer/installed.json'
            )
        );

        return $container;
    }
}

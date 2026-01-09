<?php

declare(strict_types=1);

namespace Rector\SwissKnife\DependencyInjection;

use Entropy\Container\Container;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

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

        $container->service(
            SymfonyStyle::class,
            static fn (): SymfonyStyle => new SymfonyStyle(new ArrayInput([]), new ConsoleOutput())
        );

        return $container;
    }
}

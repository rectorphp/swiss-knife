<?php

declare (strict_types=1);
namespace Rector\SwissKnife\DependencyInjection;

use SwissKnife202606\Entropy\Container\Container;
use SwissKnife202606\PhpParser\Parser;
use SwissKnife202606\PhpParser\ParserFactory;
use SwissKnife202606\Symfony\Component\Console\Input\ArrayInput;
use SwissKnife202606\Symfony\Component\Console\Output\ConsoleOutput;
use SwissKnife202606\Symfony\Component\Console\Style\SymfonyStyle;
/**
 * @api used in tests
 */
final class ContainerFactory
{
    public function create() : Container
    {
        $container = new Container();
        $container->autodiscover(__DIR__ . '/../Command');
        $container->service(Parser::class, static function () : Parser {
            $phpParserFactory = new ParserFactory();
            return $phpParserFactory->createForNewestSupportedVersion();
        });
        $container->service(SymfonyStyle::class, static function () : SymfonyStyle {
            return new SymfonyStyle(new ArrayInput([]), new ConsoleOutput());
        });
        return $container;
    }
}

<?php

declare(strict_types=1);

namespace Rector\SwissKnife\DependencyInjection;

use Entropy\Container\Container;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
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

        // console
        $container->service(Application::class, function (Container $container): Application {
            $application = new Application('Rector Swiss Knife');

            $commands = $container->findByContract(Command::class);
            $application->addCommands($commands);

            // remove basic command to make output clear
            $this->hideDefaultCommands($application);

            return $application;
        });

        // parser
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

    public function hideDefaultCommands(Application $application): void
    {
        $application->get('list')
            ->setHidden(true);
        $application->get('completion')
            ->setHidden(true);
        $application->get('help')
            ->setHidden(true);
    }
}

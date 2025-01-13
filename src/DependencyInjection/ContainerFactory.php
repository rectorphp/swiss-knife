<?php

declare(strict_types=1);

namespace Rector\SwissKnife\DependencyInjection;

use Illuminate\Container\Container;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Webmozart\Assert\Assert;

final class ContainerFactory
{
    /**
     * @api used in bin and tests
     */
    public function create(): Container
    {
        $container = new Container();

        // console
        $container->singleton(Application::class, function (Container $container): Application {
            $application = new Application('Rector Swiss Knife');

            $commandClasses = $this->findCommandClasses();

            // register commands
            foreach ($commandClasses as $commandClass) {
                $command = $container->make($commandClass);
                $application->add($command);
            }

            // remove basic command to make output clear
            $this->hideDefaultCommands($application);

            return $application;
        });

        // parser
        $container->singleton(Parser::class, static function (): Parser {
            $phpParserFactory = new ParserFactory();
            return $phpParserFactory->createForNewestSupportedVersion();
        });

        $container->singleton(
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

    /**
     * @return string[]
     */
    private function findCommandClasses(): array
    {
        $commandFinder = Finder::create()
            ->files()
            ->name('*Command.php')
            ->in(__DIR__ . '/../Command');

        $commandClasses = [];
        foreach ($commandFinder as $commandFile) {
            $commandClass = 'Rector\\SwissKnife\\Command\\' . $commandFile->getBasename('.php');

            // make sure it exists
            Assert::classExists($commandClass);

            $commandClasses[] = $commandClass;
        }

        return $commandClasses;
    }
}

<?php

declare(strict_types=1);

namespace Rector\SwissKnife\DependencyInjection;

use Illuminate\Container\Container;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Rector\SwissKnife\Command\CheckCommentedCodeCommand;
use Rector\SwissKnife\Command\CheckConflictsCommand;
use Rector\SwissKnife\Command\DumpEditorconfigCommand;
use Rector\SwissKnife\Command\FinalizeClassesCommand;
use Rector\SwissKnife\Command\FindMultiClassesCommand;
use Rector\SwissKnife\Command\NamespaceToPSR4Command;
use Rector\SwissKnife\Command\PrettyJsonCommand;
use Rector\SwissKnife\Command\PrivatizeConstantsCommand;
use Rector\SwissKnife\Command\ValidateFileLengthCommand;
use Rector\SwissKnife\Testing\Command\DetectUnitTestsCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

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
            $application = new Application('Easy CI toolkit');

            $commands = [
                $container->make(PrettyJsonCommand::class),
                $container->make(CheckCommentedCodeCommand::class),
                $container->make(CheckConflictsCommand::class),
                $container->make(ValidateFileLengthCommand::class),
                $container->make(DetectUnitTestsCommand::class),
                $container->make(FindMultiClassesCommand::class),
                $container->make(NamespaceToPSR4Command::class),
                $container->make(DumpEditorconfigCommand::class),
                $container->make(FinalizeClassesCommand::class),
                $container->make(PrivatizeConstantsCommand::class),
            ];

            $application->addCommands($commands);

            // remove basic command to make output clear
            $this->hideDefaultCommands($application);

            return $application;
        });

        // parser
        $container->singleton(Parser::class, static function (): Parser {
            $phpParserFactory = new ParserFactory();
            return $phpParserFactory->create(ParserFactory::PREFER_PHP7);
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
}

<?php

declare (strict_types=1);
namespace Rector\SwissKnife\DependencyInjection;

use SwissKnife202409\Illuminate\Container\Container;
use SwissKnife202409\PhpParser\Parser;
use SwissKnife202409\PhpParser\ParserFactory;
use Rector\SwissKnife\Command\CheckCommentedCodeCommand;
use Rector\SwissKnife\Command\CheckConflictsCommand;
use Rector\SwissKnife\Command\DumpEditorconfigCommand;
use Rector\SwissKnife\Command\FinalizeClassesCommand;
use Rector\SwissKnife\Command\FindMultiClassesCommand;
use Rector\SwissKnife\Command\NamespaceToPSR4Command;
use Rector\SwissKnife\Command\PrettyJsonCommand;
use Rector\SwissKnife\Command\PrivatizeConstantsCommand;
use Rector\SwissKnife\Testing\Command\DetectUnitTestsCommand;
use SwissKnife202409\Symfony\Component\Console\Application;
use SwissKnife202409\Symfony\Component\Console\Input\ArrayInput;
use SwissKnife202409\Symfony\Component\Console\Output\ConsoleOutput;
use SwissKnife202409\Symfony\Component\Console\Style\SymfonyStyle;
final class ContainerFactory
{
    /**
     * @api used in bin and tests
     */
    public function create() : Container
    {
        $container = new Container();
        // console
        $container->singleton(Application::class, function (Container $container) : Application {
            $application = new Application('Rector Swiss Knife');
            $commands = [$container->make(PrettyJsonCommand::class), $container->make(CheckCommentedCodeCommand::class), $container->make(CheckConflictsCommand::class), $container->make(DetectUnitTestsCommand::class), $container->make(FindMultiClassesCommand::class), $container->make(NamespaceToPSR4Command::class), $container->make(DumpEditorconfigCommand::class), $container->make(FinalizeClassesCommand::class), $container->make(PrivatizeConstantsCommand::class)];
            $application->addCommands($commands);
            // remove basic command to make output clear
            $this->hideDefaultCommands($application);
            return $application;
        });
        // parser
        $container->singleton(Parser::class, static function () : Parser {
            $phpParserFactory = new ParserFactory();
            return $phpParserFactory->create(ParserFactory::PREFER_PHP7);
        });
        $container->singleton(SymfonyStyle::class, static function () : SymfonyStyle {
            return new SymfonyStyle(new ArrayInput([]), new ConsoleOutput());
        });
        return $container;
    }
    public function hideDefaultCommands(Application $application) : void
    {
        $application->get('list')->setHidden(\true);
        $application->get('completion')->setHidden(\true);
        $application->get('help')->setHidden(\true);
    }
}

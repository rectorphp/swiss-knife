<?php

declare (strict_types=1);
namespace Rector\SwissKnife\DependencyInjection;

use SwissKnife202501\Illuminate\Container\Container;
use SwissKnife202501\PhpParser\Parser;
use SwissKnife202501\PhpParser\ParserFactory;
use Rector\SwissKnife\Behastan\Command\BehastanCommand;
use Rector\SwissKnife\Command\CheckCommentedCodeCommand;
use Rector\SwissKnife\Command\CheckConflictsCommand;
use Rector\SwissKnife\Command\DumpEditorconfigCommand;
use Rector\SwissKnife\Command\FinalizeClassesCommand;
use Rector\SwissKnife\Command\FindMultiClassesCommand;
use Rector\SwissKnife\Command\NamespaceToPSR4Command;
use Rector\SwissKnife\Command\PrettyJsonCommand;
use Rector\SwissKnife\Command\PrivatizeConstantsCommand;
use Rector\SwissKnife\Command\SearchRegexCommand;
use Rector\SwissKnife\Testing\Command\DetectUnitTestsCommand;
use SwissKnife202501\Symfony\Component\Console\Application;
use SwissKnife202501\Symfony\Component\Console\Input\ArrayInput;
use SwissKnife202501\Symfony\Component\Console\Output\ConsoleOutput;
use SwissKnife202501\Symfony\Component\Console\Style\SymfonyStyle;
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
            // @todo add automated way to load these, so we don't forget any
            $commands = [$container->make(PrettyJsonCommand::class), $container->make(CheckCommentedCodeCommand::class), $container->make(CheckConflictsCommand::class), $container->make(DetectUnitTestsCommand::class), $container->make(FindMultiClassesCommand::class), $container->make(NamespaceToPSR4Command::class), $container->make(DumpEditorconfigCommand::class), $container->make(FinalizeClassesCommand::class), $container->make(PrivatizeConstantsCommand::class), $container->make(BehastanCommand::class), $container->make(SearchRegexCommand::class)];
            $application->addCommands($commands);
            // remove basic command to make output clear
            $this->hideDefaultCommands($application);
            return $application;
        });
        // parser
        $container->singleton(Parser::class, static function () : Parser {
            $phpParserFactory = new ParserFactory();
            return $phpParserFactory->createForNewestSupportedVersion();
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

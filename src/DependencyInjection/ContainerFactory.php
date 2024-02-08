<?php

declare (strict_types=1);
namespace EasyCI202402\Rector\SwissKnife\DependencyInjection;

use EasyCI202402\Illuminate\Container\Container;
use EasyCI202402\Rector\SwissKnife\Command\CheckCommentedCodeCommand;
use EasyCI202402\Rector\SwissKnife\Command\CheckConflictsCommand;
use EasyCI202402\Rector\SwissKnife\Command\DumpEditorconfigCommand;
use EasyCI202402\Rector\SwissKnife\Command\FindMultiClassesCommand;
use EasyCI202402\Rector\SwissKnife\Command\NamespaceToPSR4Command;
use EasyCI202402\Rector\SwissKnife\Command\SpeedRunToolCommand;
use EasyCI202402\Rector\SwissKnife\Command\ValidateFileLengthCommand;
use EasyCI202402\Rector\SwissKnife\Testing\Command\DetectUnitTestsCommand;
use EasyCI202402\Symfony\Component\Console\Application;
use EasyCI202402\Symfony\Component\Console\Input\ArrayInput;
use EasyCI202402\Symfony\Component\Console\Output\ConsoleOutput;
use EasyCI202402\Symfony\Component\Console\Style\SymfonyStyle;
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
            $application = new Application('Easy CI toolkit');
            $commands = [$container->make(CheckCommentedCodeCommand::class), $container->make(CheckConflictsCommand::class), $container->make(ValidateFileLengthCommand::class), $container->make(DetectUnitTestsCommand::class), $container->make(FindMultiClassesCommand::class), $container->make(NamespaceToPSR4Command::class), $container->make(DumpEditorconfigCommand::class), $container->make(SpeedRunToolCommand::class)];
            $application->addCommands($commands);
            // remove basic command to make output clear
            $this->hideDefaultCommands($application);
            return $application;
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

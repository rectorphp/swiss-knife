<?php

declare (strict_types=1);
namespace Rector\SwissKnife\DependencyInjection;

use SwissKnife202601\Entropy\Container\Container;
use SwissKnife202601\PhpParser\Parser;
use SwissKnife202601\PhpParser\ParserFactory;
use SwissKnife202601\Symfony\Component\Console\Application;
use SwissKnife202601\Symfony\Component\Console\Command\Command;
use SwissKnife202601\Symfony\Component\Console\Input\ArrayInput;
use SwissKnife202601\Symfony\Component\Console\Output\ConsoleOutput;
use SwissKnife202601\Symfony\Component\Console\Style\SymfonyStyle;
/**
 * @api used in tests
 */
final class ContainerFactory
{
    public function create() : Container
    {
        $container = new Container();
        $container->autodiscover(__DIR__ . '/../Command');
        // console
        $container->service(Application::class, function (Container $container) : Application {
            $application = new Application('Rector Swiss Knife');
            $commands = $container->findByContract(Command::class);
            $application->addCommands($commands);
            // remove basic command to make output clear
            $this->hideDefaultCommands($application);
            return $application;
        });
        // parser
        $container->service(Parser::class, static function () : Parser {
            $phpParserFactory = new ParserFactory();
            return $phpParserFactory->createForNewestSupportedVersion();
        });
        $container->service(SymfonyStyle::class, static function () : SymfonyStyle {
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

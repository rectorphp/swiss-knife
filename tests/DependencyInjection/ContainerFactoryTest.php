<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\DependencyInjection;

use Entropy\Container\Container;
use Entropy\Console\Contract\CommandInterface;
use PhpParser\Parser;
use Rector\SwissKnife\Command\CheckConflictsCommand;
use Rector\SwissKnife\DependencyInjection\ContainerFactory;
use Rector\SwissKnife\Testing\Command\DetectUnitTestsCommand;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class ContainerFactoryTest extends AbstractTestCase
{
    public function testCreate(): void
    {
        $containerFactory = new ContainerFactory();
        $container = $containerFactory->create();

        $this->assertInstanceOf(Container::class, $container);
        $this->assertInstanceOf(Parser::class, $container->make(Parser::class));
        $this->assertInstanceOf(CheckConflictsCommand::class, $container->make(CheckConflictsCommand::class));
        $this->assertInstanceOf(DetectUnitTestsCommand::class, $container->make(DetectUnitTestsCommand::class));
    }

    public function testCommandsImplementCommandInterface(): void
    {
        $containerFactory = new ContainerFactory();
        $container = $containerFactory->create();

        $checkConflictsCommand = $container->make(CheckConflictsCommand::class);
        $this->assertInstanceOf(CommandInterface::class, $checkConflictsCommand);
    }
}

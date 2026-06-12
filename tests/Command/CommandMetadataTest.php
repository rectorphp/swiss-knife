<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command;

use Entropy\Console\Contract\CommandInterface;
use Rector\SwissKnife\Command\AliceYamlFixturesToPhpCommand;
use Rector\SwissKnife\Command\CheckCommentedCodeCommand;
use Rector\SwissKnife\Command\CheckConflictsCommand;
use Rector\SwissKnife\Command\DumpEditorconfigCommand;
use Rector\SwissKnife\Command\FinalizeClassesCommand;
use Rector\SwissKnife\Command\FindMultiClassesCommand;
use Rector\SwissKnife\Command\GenerateSymfonyConfigBuildersCommand;
use Rector\SwissKnife\Command\GenerateSymfonySmokeTestsCommand;
use Rector\SwissKnife\Command\NamespaceToPSR4Command;
use Rector\SwissKnife\Command\PrettyJsonCommand;
use Rector\SwissKnife\Command\PrivatizeConstantsCommand;
use Rector\SwissKnife\Command\SearchRegexCommand;
use Rector\SwissKnife\Command\SplitSymfonyConfigToPerPackageCommand;
use Rector\SwissKnife\Command\SpotLazyTraitsCommand;
use Rector\SwissKnife\Testing\Command\DetectUnitTestsCommand;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class CommandMetadataTest extends AbstractTestCase
{
    /**
     * @return iterable<string, array{class-string<CommandInterface>}>
     */
    public static function provideCommands(): iterable
    {
        yield 'check-conflicts' => [CheckConflictsCommand::class];
        yield 'check-commented-code' => [CheckCommentedCodeCommand::class];
        yield 'dump-editorconfig' => [DumpEditorconfigCommand::class];
        yield 'finalize-classes' => [FinalizeClassesCommand::class];
        yield 'find-multi-classes' => [FindMultiClassesCommand::class];
        yield 'generate-symfony-config-builders' => [GenerateSymfonyConfigBuildersCommand::class];
        yield 'generate-symfony-smoke-tests' => [GenerateSymfonySmokeTestsCommand::class];
        yield 'namespace-to-psr-4' => [NamespaceToPSR4Command::class];
        yield 'pretty-json' => [PrettyJsonCommand::class];
        yield 'privatize-constants' => [PrivatizeConstantsCommand::class];
        yield 'search-regex' => [SearchRegexCommand::class];
        yield 'split-config-per-package' => [SplitSymfonyConfigToPerPackageCommand::class];
        yield 'spot-lazy-traits' => [SpotLazyTraitsCommand::class];
        yield 'alice-yaml-fixtures-to-php' => [AliceYamlFixturesToPhpCommand::class];
        yield 'detect-unit-tests' => [DetectUnitTestsCommand::class];
    }

    /**
     * @param class-string<CommandInterface> $commandClass
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provideCommands')]
    public function testMetadata(string $commandClass): void
    {
        $command = $this->make($commandClass);

        $this->assertNotEmpty($command->getName());
        $this->assertNotEmpty($command->getDescription());
    }
}

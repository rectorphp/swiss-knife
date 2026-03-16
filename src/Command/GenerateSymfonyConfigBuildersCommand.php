<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Command;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Rector\SwissKnife\Enum\SymfonyExtensionClass;
use ReflectionClass;
use Symfony\Component\Config\Builder\ConfigBuilderGenerator;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * @see https://github.com/nelmio/alice/blob/v2.3.0/doc/complete-reference.md#php
 */
final readonly class GenerateSymfonyConfigBuildersCommand implements CommandInterface
{
    /**
     * @var string[]
     */
    private const array EXTENSION_CLASSES = [
        SymfonyExtensionClass::MONOLOG,
        SymfonyExtensionClass::SECURITY,
        SymfonyExtensionClass::TWIG,
        SymfonyExtensionClass::DOCTRINE,
        SymfonyExtensionClass::FRAMEWORK,
        SymfonyExtensionClass::DOCTRINE_MIGRATIONS,
        SymfonyExtensionClass::SENTRY,
        SymfonyExtensionClass::WEBPROFILER,
    ];

    public function __construct(
        private SymfonyStyle $symfonyStyle,
    ) {
    }

    public function getName(): string
    {
        return 'generate-symfony-config-builders';
    }

    public function getDescription(): string
    {
        return 'Generate Symfony config classes to /var/cache/Symfony directory, see https://symfony.com/blog/new-in-symfony-5-3-config-builder-classes';
    }

    public function run(InputInterface $input, OutputInterface $output): int
    {
        // make sure the classes exist
        if (! class_exists(ConfigBuilderGenerator::class) || ! class_exists(ContainerBuilder::class)) {
            $this->symfonyStyle->error(
                'This command requires symfony/config and symfony/dependency-injection 5.3+ to run. Update your dependencies or install them first.'
            );

            return ExitCode::ERROR;
        }

        $configBuilderGenerator = new ConfigBuilderGenerator(getcwd() . '/var/cache');
        $this->symfonyStyle->newLine();

        foreach (self::EXTENSION_CLASSES as $extensionClass) {
            // skip for non-existing classes
            if (! class_exists($extensionClass)) {
                continue;
            }

            $configuration = $this->createExtensionConfiguration($extensionClass);
            if (! $configuration instanceof ConfigurationInterface) {
                continue;
            }

            $extensionShortClass = (new ReflectionClass($extensionClass))->getShortName();
            $configBuilderGenerator->build($configuration);

            $this->symfonyStyle->writeln(sprintf('Generated "%s" class in /var/cache/Symfony', $extensionShortClass));
        }

        $this->symfonyStyle->success('Done');

        return ExitCode::SUCCESS;
    }

    /**
     * @param class-string $extensionClass
     */
    private function createExtensionConfiguration(string $extensionClass): ?ConfigurationInterface
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->setParameter('kernel.debug', false);

        /** @var Extension $extension */
        $extension = new $extensionClass();

        return $extension->getConfiguration([], $containerBuilder);
    }
}

<?php

declare (strict_types=1);
namespace SwissKnife202606\TomasVotruba\ClassLeak\Filtering;

use SwissKnife202606\TomasVotruba\ClassLeak\ValueObject\FileWithClass;
use SwissKnife202606\Webmozart\Assert\Assert;
final class PossiblyUnusedClassesFilter
{
    /**
     * These class types are used by some kind of collector pattern. Either loaded magically, registered only in config,
     * an entry point or a tagged extensions.
     *
     * @var string[]
     */
    private const DEFAULT_TYPES_TO_SKIP = [
        // http-kernel
        'SwissKnife202606\\Symfony\\Component\\Console\\Application',
        'SwissKnife202606\\Symfony\\Component\\HttpKernel\\DependencyInjection\\Extension',
        'SwissKnife202606\\Symfony\\Bundle\\FrameworkBundle\\Controller\\Controller',
        'SwissKnife202606\\Symfony\\Bundle\\FrameworkBundle\\Controller\\AbstractController',
        'SwissKnife202606\\Livewire\\Component',
        'SwissKnife202606\\Illuminate\\Routing\\Controller',
        'SwissKnife202606\\Illuminate\\Contracts\\Http\\Kernel',
        'SwissKnife202606\\Illuminate\\Support\\ServiceProvider',
        // events
        'SwissKnife202606\\Symfony\\Component\\EventDispatcher\\EventSubscriberInterface',
        'SwissKnife202606\\Symfony\\Component\\Form\\FormTypeExtensionInterface',
        'SwissKnife202606\\Symfony\\Component\\Security\\Core\\Authentication\\SimpleAuthenticatorInterface',
        'SwissKnife202606\\Vich\\UploaderBundle\\Naming\\DirectoryNamerInterface',
        // validator
        'SwissKnife202606\\Symfony\\Component\\Validator\\Constraint',
        'SwissKnife202606\\Symfony\\Component\\Validator\\ConstraintValidator',
        'SwissKnife202606\\Symfony\\Component\\Validator\\ConstraintValidatorInterface',
        'SwissKnife202606\\Symfony\\Component\\Security\\Core\\Authorization\\Voter\\VoterInterface',
        'SwissKnife202606\\Symfony\\Component\\Security\\Http\\Logout\\LogoutSuccessHandlerInterface',
        'SwissKnife202606\\Symfony\\Component\\Security\\Http\\Authentication\\AuthenticationSuccessHandlerInterface',
        'SwissKnife202606\\Symfony\\Component\\Security\\Http\\Authorization\\AccessDeniedHandlerInterface',
        'SwissKnife202606\\Symfony\\Component\\Security\\Http\\Authentication\\AuthenticationFailureHandlerInterface',
        // symfony forms
        'SwissKnife202606\\Symfony\\Component\\ExpressionLanguage\\ExpressionFunctionProviderInterface',
        'SwissKnife202606\\Symfony\\Component\\Form\\AbstractType',
        // doctrine
        'SwissKnife202606\\Doctrine\\Common\\DataFixtures\\FixtureInterface',
        'SwissKnife202606\\Doctrine\\Common\\EventSubscriber',
        'SwissKnife202606\\Nelmio\\Alice\\ProcessorInterface',
        // kernel
        'SwissKnife202606\\Symfony\\Component\\HttpKernel\\Bundle\\BundleInterface',
        'SwissKnife202606\\Symfony\\Component\\HttpKernel\\KernelInterface',
        'Symfony\\Component\\DependencyInjection\\Loader\\Configurator\\ContainerConfigurator',
        // console
        'SwissKnife202606\\Symfony\\Component\\Console\\Command\\Command',
        'SwissKnife202606\\Entropy\\Console\\Contract\\CommandInterface',
        'SwissKnife202606\\Twig\\Extension\\ExtensionInterface',
        'SwissKnife202606\\PhpCsFixer\\Fixer\\FixerInterface',
        'SwissKnife202606\\PHPUnit\\Framework\\TestCase',
        'SwissKnife202606\\PHPStan\\Rules\\Rule',
        'SwissKnife202606\\PHPStan\\Command\\ErrorFormatter\\ErrorFormatter',
        // tests
        'SwissKnife202606\\Behat\\Behat\\Context\\Context',
        // jms
        'SwissKnife202606\\JMS\\Serializer\\Handler\\SubscribingHandlerInterface',
        // laravel
        'SwissKnife202606\\Illuminate\\Support\\ServiceProvider',
        'SwissKnife202606\\Illuminate\\Foundation\\Http\\Kernel',
        'SwissKnife202606\\Illuminate\\Contracts\\Console\\Kernel',
        'SwissKnife202606\\Illuminate\\Routing\\Controller',
        // Doctrine
        'SwissKnife202606\\Doctrine\\Migrations\\AbstractMigration',
    ];
    /**
     * @var string[]
     */
    private const DEFAULT_ATTRIBUTES_TO_SKIP = [
        // Symfony
        'SwissKnife202606\\Symfony\\Component\\Console\\Attribute\\AsCommand',
        'SwissKnife202606\\Symfony\\Component\\HttpKernel\\Attribute\\AsController',
        'SwissKnife202606\\Symfony\\Component\\EventDispatcher\\Attribute\\AsEventListener',
    ];
    /**
     * @param FileWithClass[] $filesWithClasses
     * @param string[] $usedClassNames
     * @param string[] $typesToSkip
     * @param string[] $suffixesToSkip
     * @param string[] $attributesToSkip
     *
     * @return FileWithClass[]
     */
    public function filter(array $filesWithClasses, array $usedClassNames, array $typesToSkip, array $suffixesToSkip, array $attributesToSkip, bool $shouldIncludeEntities) : array
    {
        Assert::allString($usedClassNames);
        Assert::allString($typesToSkip);
        Assert::allString($suffixesToSkip);
        $possiblyUnusedFilesWithClasses = [];
        $typesToSkip = \array_merge($typesToSkip, self::DEFAULT_TYPES_TO_SKIP);
        $attributesToSkip = \array_merge($attributesToSkip, self::DEFAULT_ATTRIBUTES_TO_SKIP);
        foreach ($filesWithClasses as $fileWithClass) {
            if (\in_array($fileWithClass->getClassName(), $usedClassNames, \true)) {
                continue;
            }
            // is excluded interfaces?
            if ($this->shouldSkip($fileWithClass->getClassName(), $typesToSkip)) {
                continue;
            }
            if ($shouldIncludeEntities === \false && $fileWithClass->isEntity()) {
                continue;
            }
            if ($fileWithClass->isSerialized()) {
                continue;
            }
            // is excluded suffix?
            foreach ($suffixesToSkip as $suffixToSkip) {
                if (\substr_compare($fileWithClass->getClassName(), $suffixToSkip, -\strlen($suffixToSkip)) === 0) {
                    continue 2;
                }
            }
            // is excluded attributes?
            foreach ($fileWithClass->getAttributes() as $attribute) {
                if ($this->shouldSkip($attribute, $attributesToSkip)) {
                    continue 2;
                }
            }
            $possiblyUnusedFilesWithClasses[] = $fileWithClass;
        }
        return $possiblyUnusedFilesWithClasses;
    }
    /**
     * @param string[] $skips
     */
    private function shouldSkip(string $type, array $skips) : bool
    {
        foreach ($skips as $skip) {
            if (\strpos($type, '*') === \false && \is_a($type, $skip, \true)) {
                return \true;
            }
            if (\fnmatch($skip, $type, \FNM_NOESCAPE)) {
                return \true;
            }
        }
        return \false;
    }
}

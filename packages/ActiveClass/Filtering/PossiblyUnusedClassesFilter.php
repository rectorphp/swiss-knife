<?php

declare (strict_types=1);
namespace Symplify\EasyCI\ActiveClass\Filtering;

use Symplify\EasyCI\ActiveClass\ValueObject\FileWithClass;
use Symplify\EasyCI\ValueObject\Option;
use EasyCI20220202\Symplify\PackageBuilder\Parameter\ParameterProvider;
final class PossiblyUnusedClassesFilter
{
    /**
     * These class types are used by some kind of collector pattern. Either loaded magically, registered only in config,
     * an entry point or a tagged extensions.
     *
     * @var string[]
     */
    private const DEFAULT_TYPES_TO_SKIP = ['EasyCI20220202\\Symfony\\Bundle\\FrameworkBundle\\Controller\\AbstractController', 'EasyCI20220202\\Symfony\\Component\\HttpKernel\\Bundle\\BundleInterface', 'EasyCI20220202\\Symfony\\Component\\HttpKernel\\KernelInterface', 'EasyCI20220202\\Symfony\\Component\\Console\\Command\\Command', 'EasyCI20220202\\Twig\\Extension\\ExtensionInterface', 'EasyCI20220202\\PhpCsFixer\\Fixer\\FixerInterface', 'EasyCI20220202\\PHPUnit\\Framework\\TestCase', 'EasyCI20220202\\PHPStan\\Rules\\Rule', 'EasyCI20220202\\PHPStan\\Command\\ErrorFormatter\\ErrorFormatter'];
    /**
     * @var \Symplify\PackageBuilder\Parameter\ParameterProvider
     */
    private $parameterProvider;
    public function __construct(\EasyCI20220202\Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider)
    {
        $this->parameterProvider = $parameterProvider;
    }
    /**
     * @param FileWithClass[] $filesWithClasses
     * @param string[] $usedNames
     * @return FileWithClass[]
     */
    public function filter(array $filesWithClasses, array $usedNames) : array
    {
        $possiblyUnusedFilesWithClasses = [];
        $typesToSkip = $this->parameterProvider->provideArrayParameter(\Symplify\EasyCI\ValueObject\Option::TYPES_TO_SKIP);
        $typesToSkip = \array_merge($typesToSkip, self::DEFAULT_TYPES_TO_SKIP);
        foreach ($filesWithClasses as $fileWithClass) {
            if (\in_array($fileWithClass->getClassName(), $usedNames, \true)) {
                continue;
            }
            // is excluded interfaces?
            foreach ($typesToSkip as $typeToSkip) {
                if ($this->isClassSkipped($fileWithClass, $typeToSkip)) {
                    continue 2;
                }
            }
            $possiblyUnusedFilesWithClasses[] = $fileWithClass;
        }
        return $possiblyUnusedFilesWithClasses;
    }
    private function isClassSkipped(\Symplify\EasyCI\ActiveClass\ValueObject\FileWithClass $fileWithClass, string $typeToSkip) : bool
    {
        if (\strpos($typeToSkip, '*') === \false) {
            return \is_a($fileWithClass->getClassName(), $typeToSkip, \true);
        }
        // try fnmatch
        return \fnmatch($typeToSkip, $fileWithClass->getClassName(), \FNM_NOESCAPE);
    }
}

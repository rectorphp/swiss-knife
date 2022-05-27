<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Config;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCI\ValueObject\Option;
use EasyCI20220527\Webmozart\Assert\Assert;
final class EasyCIConfig extends \Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator
{
    /**
     * @param string[] $paths
     */
    public function excludeCheckPaths(array $paths) : void
    {
        \EasyCI20220527\Webmozart\Assert\Assert::allString($paths);
        $parameters = $this->parameters();
        $parameters->set(\Symplify\EasyCI\ValueObject\Option::EXCLUDED_CHECK_PATHS, $paths);
    }
    /**
     * @param string[] $typesToSkip
     */
    public function typesToSkip(array $typesToSkip) : void
    {
        \EasyCI20220527\Webmozart\Assert\Assert::allString($typesToSkip);
        $parameters = $this->parameters();
        $parameters->set(\Symplify\EasyCI\ValueObject\Option::TYPES_TO_SKIP, $typesToSkip);
    }
}

<?php

declare (strict_types=1);
namespace EasyCI20220607\Symplify\EasyCI\Config;

use EasyCI20220607\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use EasyCI20220607\Symplify\EasyCI\ValueObject\Option;
use EasyCI20220607\Webmozart\Assert\Assert;
final class EasyCIConfig extends ContainerConfigurator
{
    /**
     * @param string[] $paths
     */
    public function excludeCheckPaths(array $paths) : void
    {
        Assert::allString($paths);
        $parameters = $this->parameters();
        $parameters->set(Option::EXCLUDED_CHECK_PATHS, $paths);
    }
    /**
     * @param string[] $typesToSkip
     */
    public function typesToSkip(array $typesToSkip) : void
    {
        Assert::allString($typesToSkip);
        $parameters = $this->parameters();
        $parameters->set(Option::TYPES_TO_SKIP, $typesToSkip);
    }
}

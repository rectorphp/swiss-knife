<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Kernel;

use EasyCI202208\Psr\Container\ContainerInterface;
use EasyCI202208\Symplify\Astral\ValueObject\AstralConfig;
use EasyCI202208\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonManipulatorConfig;
use EasyCI202208\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyCIKernel extends AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : ContainerInterface
    {
        $configFiles[] = __DIR__ . '/../../config/config.php';
        $configFiles[] = ComposerJsonManipulatorConfig::FILE_PATH;
        $configFiles[] = AstralConfig::FILE_PATH;
        return $this->create($configFiles);
    }
}

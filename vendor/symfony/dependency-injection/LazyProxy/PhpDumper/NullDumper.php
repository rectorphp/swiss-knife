<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace EasyCI20220120\Symfony\Component\DependencyInjection\LazyProxy\PhpDumper;

use EasyCI20220120\Symfony\Component\DependencyInjection\Definition;
/**
 * Null dumper, negates any proxy code generation for any given service definition.
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 *
 * @final
 */
class NullDumper implements \EasyCI20220120\Symfony\Component\DependencyInjection\LazyProxy\PhpDumper\DumperInterface
{
    /**
     * {@inheritdoc}
     */
    public function isProxyCandidate(\EasyCI20220120\Symfony\Component\DependencyInjection\Definition $definition) : bool
    {
        return \false;
    }
    /**
     * {@inheritdoc}
     */
    public function getProxyFactoryCode(\EasyCI20220120\Symfony\Component\DependencyInjection\Definition $definition, string $id, string $factoryCode) : string
    {
        return '';
    }
    /**
     * {@inheritdoc}
     */
    public function getProxyCode(\EasyCI20220120\Symfony\Component\DependencyInjection\Definition $definition) : string
    {
        return '';
    }
}
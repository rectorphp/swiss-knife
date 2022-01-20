<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace EasyCI20220120\Symfony\Component\DependencyInjection\Compiler;

use EasyCI20220120\Symfony\Component\DependencyInjection\Alias;
use EasyCI20220120\Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use EasyCI20220120\Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument;
use EasyCI20220120\Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use EasyCI20220120\Symfony\Component\DependencyInjection\ContainerBuilder;
use EasyCI20220120\Symfony\Component\DependencyInjection\Definition;
use EasyCI20220120\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use EasyCI20220120\Symfony\Component\DependencyInjection\Reference;
use EasyCI20220120\Symfony\Component\DependencyInjection\ServiceLocator;
/**
 * Applies the "container.service_locator" tag by wrapping references into ServiceClosureArgument instances.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
final class ServiceLocatorTagPass extends \EasyCI20220120\Symfony\Component\DependencyInjection\Compiler\AbstractRecursivePass
{
    use PriorityTaggedServiceTrait;
    /**
     * @param mixed $value
     * @return mixed
     */
    protected function processValue($value, bool $isRoot = \false)
    {
        if ($value instanceof \EasyCI20220120\Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument) {
            if ($value->getTaggedIteratorArgument()) {
                $value->setValues($this->findAndSortTaggedServices($value->getTaggedIteratorArgument(), $this->container));
            }
            return self::register($this->container, $value->getValues());
        }
        if (!$value instanceof \EasyCI20220120\Symfony\Component\DependencyInjection\Definition || !$value->hasTag('container.service_locator')) {
            return parent::processValue($value, $isRoot);
        }
        if (!$value->getClass()) {
            $value->setClass(\EasyCI20220120\Symfony\Component\DependencyInjection\ServiceLocator::class);
        }
        $services = $value->getArguments()[0] ?? null;
        if ($services instanceof \EasyCI20220120\Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument) {
            $services = $this->findAndSortTaggedServices($services, $this->container);
        }
        if (!\is_array($services)) {
            throw new \EasyCI20220120\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Invalid definition for service "%s": an array of references is expected as first argument when the "container.service_locator" tag is set.', $this->currentId));
        }
        $i = 0;
        foreach ($services as $k => $v) {
            if ($v instanceof \EasyCI20220120\Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument) {
                continue;
            }
            if (!$v instanceof \EasyCI20220120\Symfony\Component\DependencyInjection\Reference) {
                throw new \EasyCI20220120\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Invalid definition for service "%s": an array of references is expected as first argument when the "container.service_locator" tag is set, "%s" found for key "%s".', $this->currentId, \get_debug_type($v), $k));
            }
            if ($i === $k) {
                unset($services[$k]);
                $k = (string) $v;
                ++$i;
            } elseif (\is_int($k)) {
                $i = null;
            }
            $services[$k] = new \EasyCI20220120\Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument($v);
        }
        \ksort($services);
        $value->setArgument(0, $services);
        $id = '.service_locator.' . \EasyCI20220120\Symfony\Component\DependencyInjection\ContainerBuilder::hash($value);
        if ($isRoot) {
            if ($id !== $this->currentId) {
                $this->container->setAlias($id, new \EasyCI20220120\Symfony\Component\DependencyInjection\Alias($this->currentId, \false));
            }
            return $value;
        }
        $this->container->setDefinition($id, $value->setPublic(\false));
        return new \EasyCI20220120\Symfony\Component\DependencyInjection\Reference($id);
    }
    /**
     * @param Reference[] $refMap
     */
    public static function register(\EasyCI20220120\Symfony\Component\DependencyInjection\ContainerBuilder $container, array $refMap, string $callerId = null) : \EasyCI20220120\Symfony\Component\DependencyInjection\Reference
    {
        foreach ($refMap as $id => $ref) {
            if (!$ref instanceof \EasyCI20220120\Symfony\Component\DependencyInjection\Reference) {
                throw new \EasyCI20220120\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Invalid service locator definition: only services can be referenced, "%s" found for key "%s". Inject parameter values using constructors instead.', \get_debug_type($ref), $id));
            }
            $refMap[$id] = new \EasyCI20220120\Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument($ref);
        }
        $locator = (new \EasyCI20220120\Symfony\Component\DependencyInjection\Definition(\EasyCI20220120\Symfony\Component\DependencyInjection\ServiceLocator::class))->addArgument($refMap)->addTag('container.service_locator');
        if (null !== $callerId && $container->hasDefinition($callerId)) {
            $locator->setBindings($container->getDefinition($callerId)->getBindings());
        }
        if (!$container->hasDefinition($id = '.service_locator.' . \EasyCI20220120\Symfony\Component\DependencyInjection\ContainerBuilder::hash($locator))) {
            $container->setDefinition($id, $locator);
        }
        if (null !== $callerId) {
            $locatorId = $id;
            // Locators are shared when they hold the exact same list of factories;
            // to have them specialized per consumer service, we use a cloning factory
            // to derivate customized instances from the prototype one.
            $container->register($id .= '.' . $callerId, \EasyCI20220120\Symfony\Component\DependencyInjection\ServiceLocator::class)->setFactory([new \EasyCI20220120\Symfony\Component\DependencyInjection\Reference($locatorId), 'withContext'])->addTag('container.service_locator_context', ['id' => $callerId])->addArgument($callerId)->addArgument(new \EasyCI20220120\Symfony\Component\DependencyInjection\Reference('service_container'));
        }
        return new \EasyCI20220120\Symfony\Component\DependencyInjection\Reference($id);
    }
}
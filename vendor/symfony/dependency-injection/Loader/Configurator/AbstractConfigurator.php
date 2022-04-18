<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace EasyCI20220418\Symfony\Component\DependencyInjection\Loader\Configurator;

use EasyCI20220418\Symfony\Component\Config\Loader\ParamConfigurator;
use EasyCI20220418\Symfony\Component\DependencyInjection\Argument\AbstractArgument;
use EasyCI20220418\Symfony\Component\DependencyInjection\Argument\ArgumentInterface;
use EasyCI20220418\Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use EasyCI20220418\Symfony\Component\DependencyInjection\Definition;
use EasyCI20220418\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use EasyCI20220418\Symfony\Component\DependencyInjection\Parameter;
use EasyCI20220418\Symfony\Component\DependencyInjection\Reference;
use EasyCI20220418\Symfony\Component\ExpressionLanguage\Expression;
abstract class AbstractConfigurator
{
    public const FACTORY = 'unknown';
    /**
     * @var callable(mixed, bool $allowService)|null
     */
    public static $valuePreProcessor;
    /** @internal */
    protected $definition = null;
    public function __call(string $method, array $args)
    {
        if (\method_exists($this, 'set' . $method)) {
            return $this->{'set' . $method}(...$args);
        }
        throw new \BadMethodCallException(\sprintf('Call to undefined method "%s::%s()".', static::class, $method));
    }
    public function __sleep() : array
    {
        throw new \BadMethodCallException('Cannot serialize ' . __CLASS__);
    }
    public function __wakeup()
    {
        throw new \BadMethodCallException('Cannot unserialize ' . __CLASS__);
    }
    /**
     * Checks that a value is valid, optionally replacing Definition and Reference configurators by their configure value.
     *
     * @param bool $allowServices whether Definition and Reference are allowed; by default, only scalars and arrays are
     *
     * @return mixed the value, optionally cast to a Definition/Reference
     * @param mixed $value
     */
    public static function processValue($value, bool $allowServices = \false)
    {
        if (\is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = static::processValue($v, $allowServices);
            }
            return self::$valuePreProcessor ? (self::$valuePreProcessor)($value, $allowServices) : $value;
        }
        if (self::$valuePreProcessor) {
            $value = (self::$valuePreProcessor)($value, $allowServices);
        }
        if ($value instanceof \EasyCI20220418\Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator) {
            $reference = new \EasyCI20220418\Symfony\Component\DependencyInjection\Reference($value->id, $value->invalidBehavior);
            return $value instanceof \EasyCI20220418\Symfony\Component\DependencyInjection\Loader\Configurator\ClosureReferenceConfigurator ? new \EasyCI20220418\Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument($reference) : $reference;
        }
        if ($value instanceof \EasyCI20220418\Symfony\Component\DependencyInjection\Loader\Configurator\InlineServiceConfigurator) {
            $def = $value->definition;
            $value->definition = null;
            return $def;
        }
        if ($value instanceof \EasyCI20220418\Symfony\Component\Config\Loader\ParamConfigurator) {
            return (string) $value;
        }
        if ($value instanceof self) {
            throw new \EasyCI20220418\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('"%s()" can be used only at the root of service configuration files.', $value::FACTORY));
        }
        switch (\true) {
            case null === $value:
            case \is_scalar($value):
                return $value;
            case $value instanceof \EasyCI20220418\Symfony\Component\DependencyInjection\Argument\ArgumentInterface:
            case $value instanceof \EasyCI20220418\Symfony\Component\DependencyInjection\Definition:
            case $value instanceof \EasyCI20220418\Symfony\Component\ExpressionLanguage\Expression:
            case $value instanceof \EasyCI20220418\Symfony\Component\DependencyInjection\Parameter:
            case $value instanceof \EasyCI20220418\Symfony\Component\DependencyInjection\Argument\AbstractArgument:
            case $value instanceof \EasyCI20220418\Symfony\Component\DependencyInjection\Reference:
                if ($allowServices) {
                    return $value;
                }
        }
        throw new \EasyCI20220418\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Cannot use values of type "%s" in service configuration files.', \get_debug_type($value)));
    }
}

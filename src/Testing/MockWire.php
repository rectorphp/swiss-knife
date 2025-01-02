<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Testing;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use Webmozart\Assert\Assert;

/**
 * @api used in public
 *
 * Service to help creating empty objects with constructor dependencies,
 * pass provided parameters and mock the rest of dependencies.
 *
 * @see \Rector\SwissKnife\Tests\Testing\MockWire\MockWireTest
 */
final class MockWire
{
    /**
     * @api used in public
     *
     * @template TObject as object
     *
     * @param class-string<TObject> $class
     * @param object[] $constructorDependencies
     *
     * @return TObject
     */
    public static function create(string $class, array $constructorDependencies = [])
    {
        Assert::classExists($class);
        Assert::allObject($constructorDependencies);

        if ($constructorDependencies === []) {
            throw new InvalidArgumentException(sprintf(
                'Instead of using %s::create() with an empty arguments, use new %s() directly or fetch service from container',
                self::class,
                $class
            ));
        }

        $classReflection = new ReflectionClass($class);
        $constructorClassMethod = $classReflection->getConstructor();

        if (! $constructorClassMethod instanceof \ReflectionMethod) {
            // no dependencies, create it directly
            return new $class();
        }

        $constructorMocks = [];

        foreach ($constructorClassMethod->getParameters() as $parameterReflection) {
            $constructorMocks[] = self::matchPassedMockOrCreate($constructorDependencies, $parameterReflection);
        }

        return new $class(...$constructorMocks);
    }

    /**
     * @param object[] $constructorDependencies
     * @return object|MockObject
     */
    private static function matchPassedMockOrCreate(
        array $constructorDependencies,
        ReflectionParameter $reflectionParameter
    ): object {
        if (! $reflectionParameter->getType() instanceof ReflectionNamedType) {
            throw new \InvalidArgumentException(sprintf(
                'Only typed parameters can be automocked. Provide the typehint for "%s" param',
                $reflectionParameter->getName()
            ));
        }

        $parameterType = $reflectionParameter->getType()
            ->getName();

        foreach ($constructorDependencies as $constructorDependency) {
            if ($constructorDependency instanceof MockObject) {
                $originalClassName = get_parent_class($constructorDependency);

                // does it match with current reflection parameters?
                if ($parameterType === $originalClassName) {
                    return $constructorDependency;
                }
            }

            // is bare object type equal to reflection type?
            if (get_class($constructorDependency) === $parameterType) {
                return $constructorDependency;
            }
        }

        // fallback to directly created mock
        // support for PHPUnit 10 and 9
        $testCaseReflectionClass = new ReflectionClass('PHPUnit\Framework\TestCase');
        $testCaseConstructor = $testCaseReflectionClass->getConstructor();
        if ($testCaseConstructor instanceof \ReflectionMethod && $testCaseConstructor->getNumberOfRequiredParameters() > 0) {
            $phpunitMocker = new PHPUnitMocker('testName');
        } else {
            $phpunitMocker = new PHPUnitMocker();
        }

        return $phpunitMocker->create($reflectionParameter->getType()->getName());
    }
}
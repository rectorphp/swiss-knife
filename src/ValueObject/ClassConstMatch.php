<?php

declare(strict_types=1);

namespace Rector\SwissKnife\ValueObject;

use ReflectionClass;
use Stringable;

final readonly class ClassConstMatch implements Stringable
{
    /**
     * @param class-string $className
     */
    public function __construct(
        private string $className,
        private string $constantName
    ) {
    }

    /**
     * For array_unique
     */
    public function __toString(): string
    {
        return $this->className . '_' . $this->constantName;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getConstantName(): string
    {
        return $this->constantName;
    }

    /**
     * We have to use class const file path,
     * as error file path only report use, does not contain the constant
     */
    public function getClassFileName(): string
    {
        $reflectionClass = new ReflectionClass($this->className);
        return (string) $reflectionClass->getFileName();
    }

    public function getParentClassConstMatch(): ?self
    {
        $reflectionClass = new ReflectionClass($this->className);
        if ($reflectionClass->getParentClass() === false) {
            return null;
        }

        return new self($reflectionClass->getParentClass()->getName(), $this->constantName);
    }
}

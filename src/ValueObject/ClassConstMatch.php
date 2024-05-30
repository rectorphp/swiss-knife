<?php

declare(strict_types=1);

namespace Rector\SwissKnife\ValueObject;

use ReflectionClass;

final readonly class ClassConstMatch
{
    /**
     * @param class-string $className
     */
    public function __construct(
        private string $className,
        private string $constantName
    ) {
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
        $classReflection = new ReflectionClass($this->className);
        return (string) $classReflection->getFileName();
    }

    public function getParentClassConstMatch(): ?self
    {
        $classReflection = new ReflectionClass($this->className);
        if ($classReflection->getParentClass() === false) {
            return null;
        }

        return new self($classReflection->getParentClass()->getName(), $this->constantName);
    }
}

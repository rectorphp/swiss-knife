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
}

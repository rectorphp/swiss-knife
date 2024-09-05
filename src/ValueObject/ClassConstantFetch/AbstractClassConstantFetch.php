<?php

declare(strict_types=1);

namespace Rector\SwissKnife\ValueObject\ClassConstantFetch;

use Rector\SwissKnife\Contract\ClassConstantFetchInterface;
use Rector\SwissKnife\ValueObject\ClassConstant;

abstract class AbstractClassConstantFetch implements ClassConstantFetchInterface
{
    public function __construct(
        private readonly string $className,
        private readonly string $constantName
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

    public function isClassConstantMatch(ClassConstant $classConstant): bool
    {
        if ($classConstant->getClassName() !== $this->className) {
            return false;
        }

        return $classConstant->getConstantName() === $this->constantName;
    }
}

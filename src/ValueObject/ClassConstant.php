<?php

declare(strict_types=1);

namespace Rector\SwissKnife\ValueObject;

use Webmozart\Assert\Assert;

final readonly class ClassConstant
{
    /**
     * @param class-string $className
     */
    public function __construct(
        private string $className,
        private string $constantName,
    ) {
        Assert::notEmpty($constantName);
        Assert::notEmpty($className);
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

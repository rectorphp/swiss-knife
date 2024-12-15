<?php

declare (strict_types=1);
namespace Rector\SwissKnife\ValueObject;

use SwissKnife202412\Webmozart\Assert\Assert;
final class ClassConstant
{
    /**
     * @var class-string
     * @readonly
     */
    private $className;
    /**
     * @readonly
     * @var string
     */
    private $constantName;
    /**
     * @param class-string $className
     */
    public function __construct(string $className, string $constantName)
    {
        $this->className = $className;
        $this->constantName = $constantName;
        Assert::notEmpty($constantName);
        Assert::notEmpty($className);
    }
    public function getClassName() : string
    {
        return $this->className;
    }
    public function getConstantName() : string
    {
        return $this->constantName;
    }
}

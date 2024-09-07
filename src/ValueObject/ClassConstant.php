<?php

declare (strict_types=1);
namespace Rector\SwissKnife\ValueObject;

use SwissKnife202409\Webmozart\Assert\Assert;
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
     * @readonly
     * @var int
     */
    private $line;
    /**
     * @param class-string $className
     */
    public function __construct(string $className, string $constantName, int $line)
    {
        $this->className = $className;
        $this->constantName = $constantName;
        $this->line = $line;
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
    public function getLine() : int
    {
        return $this->line;
    }
}

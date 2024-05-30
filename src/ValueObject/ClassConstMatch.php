<?php

declare (strict_types=1);
namespace Rector\SwissKnife\ValueObject;

use ReflectionClass;
final class ClassConstMatch
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
    }
    public function getClassName() : string
    {
        return $this->className;
    }
    public function getConstantName() : string
    {
        return $this->constantName;
    }
    /**
     * We have to use class const file path,
     * as error file path only report use, does not contain the constant
     */
    public function getClassFileName() : string
    {
        $classReflection = new ReflectionClass($this->className);
        return (string) $classReflection->getFileName();
    }
}

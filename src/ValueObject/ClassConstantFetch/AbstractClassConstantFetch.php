<?php

declare (strict_types=1);
namespace Rector\SwissKnife\ValueObject\ClassConstantFetch;

use Rector\SwissKnife\Contract\ClassConstantFetchInterface;
use Rector\SwissKnife\ValueObject\ClassConstant;
abstract class AbstractClassConstantFetch implements ClassConstantFetchInterface
{
    /**
     * @readonly
     */
    private string $className;
    /**
     * @readonly
     */
    private string $constantName;
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
    public function isClassConstantMatch(ClassConstant $classConstant) : bool
    {
        if ($classConstant->getClassName() !== $this->className) {
            return \false;
        }
        return $classConstant->getConstantName() === $this->constantName;
    }
}

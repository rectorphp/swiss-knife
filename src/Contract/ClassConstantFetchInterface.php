<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Contract;

use Rector\SwissKnife\ValueObject\ClassConstant;
interface ClassConstantFetchInterface
{
    public function getClassName() : string;
    public function getConstantName() : string;
    public function isClassConstantMatch(ClassConstant $classConstant) : bool;
}

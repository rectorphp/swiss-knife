<?php

declare (strict_types=1);
namespace Symplify\EasyCI\StaticDetector\ValueObject;

use EasyCI202301\PhpParser\Node\Stmt\ClassMethod;
final class StaticClassMethod
{
    /**
     * @var string
     */
    private $class;
    /**
     * @var string
     */
    private $method;
    /**
     * @var \PhpParser\Node\Stmt\ClassMethod
     */
    private $classMethod;
    public function __construct(string $class, string $method, ClassMethod $classMethod)
    {
        $this->class = $class;
        $this->method = $method;
        $this->classMethod = $classMethod;
    }
    public function getClass() : string
    {
        return $this->class;
    }
    public function getMethod() : string
    {
        return $this->method;
    }
    public function getFileLocationWithLine() : string
    {
        return $this->classMethod->getAttribute(\Symplify\EasyCI\StaticDetector\ValueObject\StaticDetectorAttributeKey::FILE_LINE);
    }
}

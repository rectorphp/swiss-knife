<?php

declare (strict_types=1);
namespace Symplify\EasyCI\StaticDetector\ValueObject;

use EasyCI202301\PhpParser\Node;
use EasyCI202301\PhpParser\Node\Expr\StaticCall;
final class StaticClassMethodWithStaticCalls
{
    /**
     * @var string[]
     */
    private $staticCallsFilePathsWithLines = [];
    /**
     * @var \Symplify\EasyCI\StaticDetector\ValueObject\StaticClassMethod
     */
    private $staticClassMethod;
    /**
     * @var StaticCall[]
     */
    private $staticCalls;
    /**
     * @param StaticCall[] $staticCalls
     */
    public function __construct(\Symplify\EasyCI\StaticDetector\ValueObject\StaticClassMethod $staticClassMethod, array $staticCalls)
    {
        $this->staticClassMethod = $staticClassMethod;
        $this->staticCalls = $staticCalls;
        $this->staticCallsFilePathsWithLines = $this->createFilePathsWithLinesFromNodes($staticCalls);
    }
    public function getStaticClassMethodName() : string
    {
        return $this->staticClassMethod->getClass() . '::' . $this->staticClassMethod->getMethod();
    }
    /**
     * @return StaticCall[]
     */
    public function getStaticCalls() : array
    {
        return $this->staticCalls;
    }
    public function getStaticCallFileLocationWithLine() : string
    {
        return $this->staticClassMethod->getFileLocationWithLine();
    }
    /**
     * @return string[]
     */
    public function getStaticCallsFilePathsWithLines() : array
    {
        return $this->staticCallsFilePathsWithLines;
    }
    public function getStaticCallsCount() : int
    {
        return \count($this->staticCallsFilePathsWithLines);
    }
    /**
     * @param Node[] $nodes
     * @return string[]
     */
    private function createFilePathsWithLinesFromNodes(array $nodes) : array
    {
        $filePathsWithLines = [];
        foreach ($nodes as $node) {
            $filePathsWithLines[] = $node->getAttribute(\Symplify\EasyCI\StaticDetector\ValueObject\StaticDetectorAttributeKey::FILE_LINE);
        }
        return $filePathsWithLines;
    }
}

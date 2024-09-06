<?php

declare (strict_types=1);
namespace Rector\SwissKnife\PhpParser\NodeVisitor;

use SwissKnife202409\PhpParser\Node;
use SwissKnife202409\PhpParser\Node\Name;
use SwissKnife202409\PhpParser\Node\Stmt\Class_;
use SwissKnife202409\PhpParser\NodeVisitorAbstract;
use Rector\SwissKnife\ValueObject\ClassConstant;
use SwissKnife202409\Webmozart\Assert\Assert;
final class FindNonPrivateClassConstNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var ClassConstant[]
     */
    private $classConstants = [];
    public function enterNode(Node $node) : ?Node
    {
        if (!$node instanceof Class_) {
            return null;
        }
        if ($node->isAnonymous()) {
            return null;
        }
        Assert::isInstanceOf($node->namespacedName, Name::class);
        $className = $node->namespacedName->toString();
        foreach ($node->getConstants() as $classConst) {
            foreach ($classConst->consts as $constConst) {
                $constantName = $constConst->name->toString();
                // not interested in private constants
                if ($classConst->isPrivate()) {
                    continue;
                }
                $this->classConstants[] = new ClassConstant($className, $constantName, $classConst->getLine());
            }
        }
        return $node;
    }
    /**
     * @return ClassConstant[]
     */
    public function getClassConstants() : array
    {
        return $this->classConstants;
    }
}

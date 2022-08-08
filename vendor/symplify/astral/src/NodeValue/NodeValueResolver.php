<?php

declare (strict_types=1);
namespace EasyCI202208\Symplify\Astral\NodeValue;

use EasyCI202208\PhpParser\ConstExprEvaluationException;
use EasyCI202208\PhpParser\ConstExprEvaluator;
use EasyCI202208\PhpParser\Node\Expr;
use EasyCI202208\PhpParser\Node\Expr\Cast;
use EasyCI202208\PhpParser\Node\Expr\Instanceof_;
use EasyCI202208\PhpParser\Node\Expr\MethodCall;
use EasyCI202208\PhpParser\Node\Expr\PropertyFetch;
use EasyCI202208\PhpParser\Node\Expr\Variable;
use EasyCI202208\Symplify\Astral\Contract\NodeValueResolver\NodeValueResolverInterface;
use EasyCI202208\Symplify\Astral\Exception\ShouldNotHappenException;
use EasyCI202208\Symplify\Astral\NodeValue\NodeValueResolver\ClassConstFetchValueResolver;
use EasyCI202208\Symplify\Astral\NodeValue\NodeValueResolver\ConstFetchValueResolver;
use EasyCI202208\Symplify\Astral\NodeValue\NodeValueResolver\FuncCallValueResolver;
use EasyCI202208\Symplify\Astral\NodeValue\NodeValueResolver\MagicConstValueResolver;
use EasyCI202208\Symplify\PackageBuilder\Php\TypeChecker;
/**
 * @see \Symplify\Astral\Tests\NodeValue\NodeValueResolverTest
 */
final class NodeValueResolver
{
    /**
     * @var \PhpParser\ConstExprEvaluator
     */
    private $constExprEvaluator;
    /**
     * @var string|null
     */
    private $currentFilePath;
    /**
     * @var NodeValueResolverInterface[]
     */
    private $nodeValueResolvers = [];
    /**
     * @var \Symplify\PackageBuilder\Php\TypeChecker
     */
    private $typeChecker;
    public function __construct(TypeChecker $typeChecker)
    {
        $this->typeChecker = $typeChecker;
        $this->constExprEvaluator = new ConstExprEvaluator(function (Expr $expr) {
            return $this->resolveByNode($expr);
        });
        $this->nodeValueResolvers[] = new ClassConstFetchValueResolver();
        $this->nodeValueResolvers[] = new ConstFetchValueResolver();
        $this->nodeValueResolvers[] = new MagicConstValueResolver();
        $this->nodeValueResolvers[] = new FuncCallValueResolver($this->constExprEvaluator);
    }
    /**
     * @return mixed
     */
    public function resolve(Expr $expr, string $filePath)
    {
        $this->currentFilePath = $filePath;
        try {
            return $this->constExprEvaluator->evaluateDirectly($expr);
        } catch (ConstExprEvaluationException $exception) {
            return null;
        }
    }
    /**
     * @return mixed
     */
    private function resolveByNode(Expr $expr)
    {
        if ($this->currentFilePath === null) {
            throw new ShouldNotHappenException();
        }
        foreach ($this->nodeValueResolvers as $nodeValueResolver) {
            if (\is_a($expr, $nodeValueResolver->getType(), \true)) {
                return $nodeValueResolver->resolve($expr, $this->currentFilePath);
            }
        }
        // these values cannot be resolved in reliable way
        if ($this->typeChecker->isInstanceOf($expr, [Variable::class, Cast::class, MethodCall::class, PropertyFetch::class, Instanceof_::class])) {
            throw new ConstExprEvaluationException();
        }
        return null;
    }
}

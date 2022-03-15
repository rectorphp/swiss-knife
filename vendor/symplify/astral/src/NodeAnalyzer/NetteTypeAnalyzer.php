<?php

declare (strict_types=1);
namespace EasyCI20220315\Symplify\Astral\NodeAnalyzer;

use EasyCI20220315\Nette\Application\UI\Template;
use EasyCI20220315\PhpParser\Node\Expr;
use EasyCI20220315\PhpParser\Node\Expr\PropertyFetch;
use EasyCI20220315\PHPStan\Analyser\Scope;
use EasyCI20220315\Symplify\Astral\Naming\SimpleNameResolver;
use EasyCI20220315\Symplify\Astral\TypeAnalyzer\ContainsTypeAnalyser;
/**
 * @api
 */
final class NetteTypeAnalyzer
{
    /**
     * @var array<class-string<Template>>
     */
    private const TEMPLATE_TYPES = ['EasyCI20220315\\Nette\\Application\\UI\\Template', 'EasyCI20220315\\Nette\\Application\\UI\\ITemplate', 'EasyCI20220315\\Nette\\Bridges\\ApplicationLatte\\Template', 'EasyCI20220315\\Nette\\Bridges\\ApplicationLatte\\DefaultTemplate'];
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    /**
     * @var \Symplify\Astral\TypeAnalyzer\ContainsTypeAnalyser
     */
    private $containsTypeAnalyser;
    public function __construct(\EasyCI20220315\Symplify\Astral\Naming\SimpleNameResolver $simpleNameResolver, \EasyCI20220315\Symplify\Astral\TypeAnalyzer\ContainsTypeAnalyser $containsTypeAnalyser)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->containsTypeAnalyser = $containsTypeAnalyser;
    }
    /**
     * E.g. $this->template->key
     */
    public function isTemplateMagicPropertyType(\EasyCI20220315\PhpParser\Node\Expr $expr, \EasyCI20220315\PHPStan\Analyser\Scope $scope) : bool
    {
        if (!$expr instanceof \EasyCI20220315\PhpParser\Node\Expr\PropertyFetch) {
            return \false;
        }
        if (!$expr->var instanceof \EasyCI20220315\PhpParser\Node\Expr\PropertyFetch) {
            return \false;
        }
        return $this->isTemplateType($expr->var, $scope);
    }
    /**
     * E.g. $this->template
     */
    public function isTemplateType(\EasyCI20220315\PhpParser\Node\Expr $expr, \EasyCI20220315\PHPStan\Analyser\Scope $scope) : bool
    {
        return $this->containsTypeAnalyser->containsExprTypes($expr, $scope, self::TEMPLATE_TYPES);
    }
    /**
     * This type has getComponent() method
     */
    public function isInsideComponentContainer(\EasyCI20220315\PHPStan\Analyser\Scope $scope) : bool
    {
        $className = $this->simpleNameResolver->getClassNameFromScope($scope);
        if ($className === null) {
            return \false;
        }
        // this type has getComponent() method
        return \is_a($className, 'EasyCI20220315\\Nette\\ComponentModel\\Container', \true);
    }
    public function isInsideControl(\EasyCI20220315\PHPStan\Analyser\Scope $scope) : bool
    {
        $className = $this->simpleNameResolver->getClassNameFromScope($scope);
        if ($className === null) {
            return \false;
        }
        return \is_a($className, 'EasyCI20220315\\Nette\\Application\\UI\\Control', \true);
    }
}

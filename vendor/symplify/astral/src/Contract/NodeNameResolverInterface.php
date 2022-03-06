<?php

declare (strict_types=1);
namespace EasyCI20220306\Symplify\Astral\Contract;

use EasyCI20220306\PhpParser\Node;
interface NodeNameResolverInterface
{
    public function match(\EasyCI20220306\PhpParser\Node $node) : bool;
    public function resolve(\EasyCI20220306\PhpParser\Node $node) : ?string;
}

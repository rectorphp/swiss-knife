<?php

declare (strict_types=1);
namespace EasyCI20220307\Symplify\Astral\Contract;

use EasyCI20220307\PhpParser\Node;
interface NodeNameResolverInterface
{
    public function match(\EasyCI20220307\PhpParser\Node $node) : bool;
    public function resolve(\EasyCI20220307\PhpParser\Node $node) : ?string;
}

<?php

declare (strict_types=1);
namespace EasyCI20220206\Symplify\Astral\Contract;

use EasyCI20220206\PhpParser\Node;
interface NodeNameResolverInterface
{
    public function match(\EasyCI20220206\PhpParser\Node $node) : bool;
    public function resolve(\EasyCI20220206\PhpParser\Node $node) : ?string;
}

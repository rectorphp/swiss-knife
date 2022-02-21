<?php

declare (strict_types=1);
namespace EasyCI20220221\Symplify\Astral\Contract;

use EasyCI20220221\PhpParser\Node;
interface NodeNameResolverInterface
{
    public function match(\EasyCI20220221\PhpParser\Node $node) : bool;
    public function resolve(\EasyCI20220221\PhpParser\Node $node) : ?string;
}

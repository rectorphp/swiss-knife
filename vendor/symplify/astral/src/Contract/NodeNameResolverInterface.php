<?php

declare (strict_types=1);
namespace EasyCI20220516\Symplify\Astral\Contract;

use EasyCI20220516\PhpParser\Node;
interface NodeNameResolverInterface
{
    public function match(\EasyCI20220516\PhpParser\Node $node) : bool;
    public function resolve(\EasyCI20220516\PhpParser\Node $node) : ?string;
}

<?php

declare (strict_types=1);
namespace EasyCI20220120\Symplify\Astral\Contract;

use EasyCI20220120\PhpParser\Node;
interface NodeNameResolverInterface
{
    public function match(\EasyCI20220120\PhpParser\Node $node) : bool;
    public function resolve(\EasyCI20220120\PhpParser\Node $node) : ?string;
}

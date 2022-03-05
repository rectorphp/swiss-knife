<?php

declare (strict_types=1);
namespace EasyCI20220305\Symplify\Astral\Contract;

use EasyCI20220305\PhpParser\Node;
interface NodeNameResolverInterface
{
    public function match(\EasyCI20220305\PhpParser\Node $node) : bool;
    public function resolve(\EasyCI20220305\PhpParser\Node $node) : ?string;
}

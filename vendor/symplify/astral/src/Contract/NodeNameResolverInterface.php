<?php

declare (strict_types=1);
namespace EasyCI20220521\Symplify\Astral\Contract;

use EasyCI20220521\PhpParser\Node;
interface NodeNameResolverInterface
{
    public function match(\EasyCI20220521\PhpParser\Node $node) : bool;
    public function resolve(\EasyCI20220521\PhpParser\Node $node) : ?string;
}

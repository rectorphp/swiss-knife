<?php

declare (strict_types=1);
namespace EasyCI20220513\Symplify\Astral\Contract;

use EasyCI20220513\PhpParser\Node;
interface NodeNameResolverInterface
{
    public function match(\EasyCI20220513\PhpParser\Node $node) : bool;
    public function resolve(\EasyCI20220513\PhpParser\Node $node) : ?string;
}

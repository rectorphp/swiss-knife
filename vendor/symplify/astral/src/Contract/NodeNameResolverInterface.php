<?php

declare (strict_types=1);
namespace EasyCI20220416\Symplify\Astral\Contract;

use EasyCI20220416\PhpParser\Node;
interface NodeNameResolverInterface
{
    public function match(\EasyCI20220416\PhpParser\Node $node) : bool;
    public function resolve(\EasyCI20220416\PhpParser\Node $node) : ?string;
}

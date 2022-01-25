<?php

declare (strict_types=1);
namespace EasyCI20220125\PhpParser;

interface Builder
{
    /**
     * Returns the built node.
     *
     * @return Node The built node
     */
    public function getNode() : \EasyCI20220125\PhpParser\Node;
}

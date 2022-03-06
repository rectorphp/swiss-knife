<?php

declare (strict_types=1);
namespace EasyCI20220306\PhpParser;

interface Builder
{
    /**
     * Returns the built node.
     *
     * @return Node The built node
     */
    public function getNode() : \EasyCI20220306\PhpParser\Node;
}

<?php

declare (strict_types=1);
namespace SwissKnife202502\PhpParser\Node\Stmt;

use SwissKnife202502\PhpParser\Node;
/** Nop/empty statement (;). */
class Nop extends Node\Stmt
{
    public function getSubNodeNames() : array
    {
        return [];
    }
    public function getType() : string
    {
        return 'Stmt_Nop';
    }
}

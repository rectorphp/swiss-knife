<?php

namespace SwissKnife202410\Illuminate\Contracts\Database\Query;

use SwissKnife202410\Illuminate\Database\Grammar;
interface Expression
{
    /**
     * Get the value of the expression.
     *
     * @param  \Illuminate\Database\Grammar  $grammar
     * @return string|int|float
     */
    public function getValue(Grammar $grammar);
}

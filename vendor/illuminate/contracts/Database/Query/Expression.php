<?php

namespace SwissKnife202408\Illuminate\Contracts\Database\Query;

use SwissKnife202408\Illuminate\Database\Grammar;
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

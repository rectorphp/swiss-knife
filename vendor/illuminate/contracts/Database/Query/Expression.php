<?php

namespace SwissKnife202506\Illuminate\Contracts\Database\Query;

use SwissKnife202506\Illuminate\Database\Grammar;
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

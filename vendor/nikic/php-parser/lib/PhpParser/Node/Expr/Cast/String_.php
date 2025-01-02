<?php

declare (strict_types=1);
namespace SwissKnife202501\PhpParser\Node\Expr\Cast;

use SwissKnife202501\PhpParser\Node\Expr\Cast;
class String_ extends Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_String';
    }
}

<?php

declare (strict_types=1);
namespace SwissKnife202507\PhpParser\Node\Expr\Cast;

use SwissKnife202507\PhpParser\Node\Expr\Cast;
class Int_ extends Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Int';
    }
}

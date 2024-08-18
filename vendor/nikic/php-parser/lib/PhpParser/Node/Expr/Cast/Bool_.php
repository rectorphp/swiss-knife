<?php

declare (strict_types=1);
namespace SwissKnife202408\PhpParser\Node\Expr\Cast;

use SwissKnife202408\PhpParser\Node\Expr\Cast;
class Bool_ extends Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Bool';
    }
}

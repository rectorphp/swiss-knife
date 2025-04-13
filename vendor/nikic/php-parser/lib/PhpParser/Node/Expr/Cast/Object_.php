<?php

declare (strict_types=1);
namespace SwissKnife202504\PhpParser\Node\Expr\Cast;

use SwissKnife202504\PhpParser\Node\Expr\Cast;
class Object_ extends Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Object';
    }
}

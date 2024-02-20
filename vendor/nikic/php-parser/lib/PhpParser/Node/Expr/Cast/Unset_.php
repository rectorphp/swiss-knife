<?php

declare (strict_types=1);
namespace SwissKnife202402\PhpParser\Node\Expr\Cast;

use SwissKnife202402\PhpParser\Node\Expr\Cast;
class Unset_ extends Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Unset';
    }
}

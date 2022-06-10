<?php

declare (strict_types=1);
namespace EasyCI20220610\PhpParser\Node\Expr\Cast;

use EasyCI20220610\PhpParser\Node\Expr\Cast;
class Int_ extends Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Int';
    }
}

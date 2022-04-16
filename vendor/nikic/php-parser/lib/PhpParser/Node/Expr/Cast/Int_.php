<?php

declare (strict_types=1);
namespace EasyCI20220416\PhpParser\Node\Expr\Cast;

use EasyCI20220416\PhpParser\Node\Expr\Cast;
class Int_ extends \EasyCI20220416\PhpParser\Node\Expr\Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Int';
    }
}

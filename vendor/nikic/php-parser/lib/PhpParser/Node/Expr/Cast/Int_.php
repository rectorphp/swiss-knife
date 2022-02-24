<?php

declare (strict_types=1);
namespace EasyCI20220224\PhpParser\Node\Expr\Cast;

use EasyCI20220224\PhpParser\Node\Expr\Cast;
class Int_ extends \EasyCI20220224\PhpParser\Node\Expr\Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Int';
    }
}

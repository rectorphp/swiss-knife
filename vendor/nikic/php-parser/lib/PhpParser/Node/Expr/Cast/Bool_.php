<?php

declare (strict_types=1);
namespace EasyCI20220509\PhpParser\Node\Expr\Cast;

use EasyCI20220509\PhpParser\Node\Expr\Cast;
class Bool_ extends \EasyCI20220509\PhpParser\Node\Expr\Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Bool';
    }
}

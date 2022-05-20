<?php

declare (strict_types=1);
namespace EasyCI20220520\PhpParser\Node\Scalar\MagicConst;

use EasyCI20220520\PhpParser\Node\Scalar\MagicConst;
class Trait_ extends \EasyCI20220520\PhpParser\Node\Scalar\MagicConst
{
    public function getName() : string
    {
        return '__TRAIT__';
    }
    public function getType() : string
    {
        return 'Scalar_MagicConst_Trait';
    }
}

<?php

declare (strict_types=1);
namespace SwissKnife202410\PhpParser\Node\Scalar\MagicConst;

use SwissKnife202410\PhpParser\Node\Scalar\MagicConst;
class Trait_ extends MagicConst
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

<?php

namespace Rector\SwissKnife\Behastan\ValueObject;

use Rector\SwissKnife\Behastan\Contract\MaskInterface;

abstract class AbstractMask implements MaskInterface
{
    public function __construct(
        public readonly string $mask,
        public readonly string $filePath,
    ) {
    }
}

<?php

namespace Rector\SwissKnife\Behastan\ValueObject\Mask;

use Rector\SwissKnife\Behastan\Contract\MaskInterface;

abstract class AbstractMask implements MaskInterface
{
    public function __construct(
        public readonly string $mask,
        public readonly string $filePath,
        public readonly string $className,
        public readonly string $methodName,
    ) {
    }
}

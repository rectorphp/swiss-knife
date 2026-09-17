<?php

declare(strict_types=1);

namespace Rector\SwissKnife\DuplicatedCode\ValueObject;

final readonly class CodeClone
{
    public function __construct(
        public CodeCloneFile $firstFile,
        public CodeCloneFile $secondFile,
        public int $lines,
        public int $tokens
    ) {
    }
}

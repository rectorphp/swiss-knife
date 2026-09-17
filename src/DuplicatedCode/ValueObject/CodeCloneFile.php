<?php

declare(strict_types=1);

namespace Rector\SwissKnife\DuplicatedCode\ValueObject;

final readonly class CodeCloneFile
{
    public function __construct(
        public string $filePath,
        public int $startLine,
        public int $endLine
    ) {
    }
}

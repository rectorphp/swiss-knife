<?php

declare(strict_types=1);

namespace Rector\SwissKnife\DuplicatedCode\ValueObject;

final class CodeClone
{
    /**
     * @readonly
     */
    public CodeCloneFile $firstFile;

    /**
     * @readonly
     */
    public CodeCloneFile $secondFile;

    /**
     * @readonly
     */
    public int $lines;

    /**
     * @readonly
     */
    public int $tokens;

    public function __construct(CodeCloneFile $firstFile, CodeCloneFile $secondFile, int $lines, int $tokens)
    {
        $this->firstFile = $firstFile;
        $this->secondFile = $secondFile;
        $this->lines = $lines;
        $this->tokens = $tokens;
    }
}

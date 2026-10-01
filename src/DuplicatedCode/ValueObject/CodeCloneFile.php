<?php

declare(strict_types=1);

namespace Rector\SwissKnife\DuplicatedCode\ValueObject;

final class CodeCloneFile
{
    /**
     * @readonly
     */
    public string $filePath;

    /**
     * @readonly
     */
    public int $startLine;

    /**
     * @readonly
     */
    public int $endLine;

    public function __construct(string $filePath, int $startLine, int $endLine)
    {
        $this->filePath = $filePath;
        $this->startLine = $startLine;
        $this->endLine = $endLine;
    }
}

<?php

declare(strict_types=1);

namespace Rector\SwissKnife\DuplicatedCode;

use Rector\SwissKnife\DuplicatedCode\ValueObject\CodeClone;
use Rector\SwissKnife\DuplicatedCode\ValueObject\CodeCloneFile;

/**
 * Token-based copy-paste detector using a Rabin-Karp rolling hash over the
 * normalized token stream, mirroring the classic phpcpd behaviour.
 * @see \Rector\SwissKnife\Tests\DuplicatedCode\CloneDetectorTest
 */
final class CloneDetector
{
    /**
     * Tokens that carry no structural meaning for clone detection.
     *
     * @var array<int, true>
     */
    private const array IGNORED_TOKENS = [
        T_INLINE_HTML => true,
        T_COMMENT => true,
        T_DOC_COMMENT => true,
        T_OPEN_TAG => true,
        T_OPEN_TAG_WITH_ECHO => true,
        T_CLOSE_TAG => true,
        T_WHITESPACE => true,
    ];

    /**
     * Bytes contributed by each kept token to the signature: 1 type byte + 4 CRC32 bytes.
     */
    private const int BYTES_PER_TOKEN = 5;

    /**
     * First seen location per window hash: hash => [filePath, tokenIndex].
     *
     * @var array<string, array{string, int}>
     */
    private array $hashes = [];

    /**
     * Per-file token line numbers, kept for span line lookup.
     *
     * @var array<string, int[]>
     */
    private array $fileLines = [];

    /**
     * @var CodeClone[]
     */
    private array $clones = [];

    public function __construct(
        private readonly int $minLines,
        private readonly int $minTokens,
        private readonly bool $fuzzy
    ) {
    }

    /**
     * @param string[] $filePaths
     * @return CodeClone[]
     */
    public function detect(array $filePaths): array
    {
        foreach ($filePaths as $filePath) {
            $this->processFile($filePath);
        }

        return $this->clones;
    }

    private function processFile(string $filePath): void
    {
        $source = file_get_contents($filePath);
        if ($source === false) {
            return;
        }

        [$signature, $lines] = $this->tokenize($source);
        $this->fileLines[$filePath] = $lines;

        $tokenCount = count($lines);
        if ($tokenCount < $this->minTokens) {
            return;
        }

        $lastWindow = $tokenCount - $this->minTokens;
        $windowBytes = $this->minTokens * self::BYTES_PER_TOKEN;

        $found = false;
        $firstToken = 0;
        $originFile = '';
        $originToken = 0;

        for ($i = 0; $i <= $lastWindow; ++$i) {
            $hash = substr(md5(substr($signature, $i * self::BYTES_PER_TOKEN, $windowBytes), true), 0, 8);

            if (isset($this->hashes[$hash])) {
                if (! $found) {
                    $found = true;
                    $firstToken = $i;
                    [$originFile, $originToken] = $this->hashes[$hash];
                }

                continue;
            }

            if ($found) {
                $this->recordClone($originFile, $originToken, $filePath, $firstToken, $i);
                $found = false;
            }

            $this->hashes[$hash] = [$filePath, $i];
        }

        if ($found) {
            $this->recordClone($originFile, $originToken, $filePath, $firstToken, $lastWindow + 1);
        }
    }

    private function recordClone(
        string $originFile,
        int $originToken,
        string $currentFile,
        int $currentToken,
        int $mismatchWindow
    ): void {
        $windowCount = $mismatchWindow - $currentToken;
        $tokenSpan = $windowCount + $this->minTokens - 1;

        $originLines = $this->fileLines[$originFile];
        $currentLines = $this->fileLines[$currentFile];

        $originStartLine = $originLines[$originToken];
        $originEndLine = $originLines[min($originToken + $tokenSpan - 1, count($originLines) - 1)];

        $currentStartLine = $currentLines[$currentToken];
        $currentEndLine = $currentLines[min($currentToken + $tokenSpan - 1, count($currentLines) - 1)];

        $numberOfLines = $originEndLine - $originStartLine + 1;
        if ($numberOfLines < $this->minLines) {
            return;
        }

        $this->clones[] = new CodeClone(
            new CodeCloneFile($originFile, $originStartLine, $originEndLine),
            new CodeCloneFile($currentFile, $currentStartLine, $currentEndLine),
            $numberOfLines,
            $tokenSpan
        );
    }

    /**
     * Builds the token signature and the parallel line map for a source string.
     *
     * @return array{string, int[]}
     */
    private function tokenize(string $source): array
    {
        $signature = '';
        $lines = [];
        $currentLine = 1;

        foreach (token_get_all($source) as $token) {
            if (is_array($token)) {
                $tokenId = $token[0];
                $tokenText = $token[1];
                $currentLine = $token[2];

                if (isset(self::IGNORED_TOKENS[$tokenId])) {
                    $currentLine += substr_count($tokenText, "\n");
                    continue;
                }

                if ($this->fuzzy && $tokenId === T_VARIABLE) {
                    $tokenText = '$';
                }

                $signature .= chr($tokenId & 255) . pack('N*', crc32($tokenText));
                $lines[] = $currentLine;
                $currentLine += substr_count($tokenText, "\n");

                continue;
            }

            $signature .= chr(0) . pack('N*', crc32($token));
            $lines[] = $currentLine;
        }

        return [$signature, $lines];
    }
}

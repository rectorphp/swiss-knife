<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Lines\NodeVisitor;

use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

final class CommentLineCountingNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var Comment[]
     */
    private array $comments = [];

    public function enterNode(Node $node): ?Node
    {
        $this->comments = array_merge($this->comments, $node->getComments());

        return null;
    }

    public function getCommentLineCount(): int
    {
        $commentLines = [];

        foreach ($this->uniqueComments() as $comment) {
            for ($line = $comment->getStartLine(); $line <= $comment->getEndLine(); ++$line) {
                $commentLines[$line] = true;
            }
        }

        return count($commentLines);
    }

    /**
     * @return Comment[]
     */
    private function uniqueComments(): array
    {
        $uniqueComments = [];

        foreach ($this->comments as $comment) {
            $key = $comment->getStartLine() . '_' . $comment->getStartTokenPos()
                . '_' . $comment->getEndLine() . '_' . $comment->getEndTokenPos();
            $uniqueComments[$key] = $comment;
        }

        return $uniqueComments;
    }
}

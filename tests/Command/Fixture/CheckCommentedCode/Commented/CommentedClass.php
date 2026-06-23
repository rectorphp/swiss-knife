<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command\Fixture\CheckCommentedCode\Commented;

final class CommentedClass
{
    public function run(): int
    {
        // line one
        // line two
        // line three
        return 1;
    }
}

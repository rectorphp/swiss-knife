<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\PhpParser\NodeVisitor\EntityClassNameCollectingNodeVisitor\Fixture;

use Doctrine\ORM\Mapping\Entity;

#[Entity]
final class SomeEntity
{
}

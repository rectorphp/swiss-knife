<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Behastan\DefinitionMasksResolver\Fixture;

final class AnotherBehatContext
{
    /**
     * @When I click homepage
     */
    public function action(): void
    {
    }

    /**
     * @Then never used
     */
    public function deadAction(): void
    {
    }
}

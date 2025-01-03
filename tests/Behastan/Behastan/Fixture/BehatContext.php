<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Behastan\Behastan\Fixture;

final class BehatContext
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

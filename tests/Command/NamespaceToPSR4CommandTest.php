<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command;

use Entropy\Console\Mapper\CLIRequestMapper;
use Entropy\Console\ValueObject\CLIRequest;
use Rector\SwissKnife\Command\NamespaceToPSR4Command;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class NamespaceToPSR4CommandTest extends AbstractTestCase
{
    private CLIRequestMapper $cliRequestMapper;

    private NamespaceToPSR4Command $namespaceToPSR4Command;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cliRequestMapper = $this->make(CLIRequestMapper::class);
        $this->namespaceToPSR4Command = $this->make(NamespaceToPSR4Command::class);
    }

    /**
     * @see https://github.com/rectorphp/swiss-knife/issues/124
     */
    public function testNamespaceRootOptionIsKeptAsString(): void
    {
        // the input parser collects long-option values into arrays;
        // the string parameter must receive the single value, not the array cast to "Array"
        $cliRequest = new CLIRequest('namespace-to-psr-4', ['app'], [
            'namespace-root' => ['App\\'],
        ]);

        $arguments = $this->cliRequestMapper->resolveArguments($this->namespaceToPSR4Command, $cliRequest);

        $this->assertSame(['app', 'App\\'], $arguments);
    }
}

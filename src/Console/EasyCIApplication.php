<?php

declare (strict_types=1);
namespace EasyCI20220607\Symplify\EasyCI\Console;

use EasyCI20220607\Symfony\Component\Console\Application;
use EasyCI20220607\Symfony\Component\Console\Command\Command;
final class EasyCIApplication extends Application
{
    /**
     * @param Command[] $commands
     */
    public function __construct(array $commands)
    {
        $this->addCommands($commands);
        parent::__construct();
    }
}

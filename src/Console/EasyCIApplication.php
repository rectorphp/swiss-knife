<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Console;

use EasyCI20220613\Symfony\Component\Console\Application;
use EasyCI20220613\Symfony\Component\Console\Command\Command;
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

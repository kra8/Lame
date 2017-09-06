<?php

namespace Lame;

use Symfony\Component\Console\Application;
// use Command\AnalizeCommand;

// $app = new Application();
// $app->add(new AnalizeCommand($container));
// $app->run();

class Kernel
{
    private $app;

    private $commands = [
        'Lame\Command\NewCommand',
    ];

    public function __construct()
    {
        $this->app = new Application();
        foreach ($this->commands as $commandClassName) {
            $command = new $commandClassName();
            $this->app->add($command);
        }
    }

    public function run()
    {
        $this->app->run();
    }
}

<?php

declare(strict_types=1);

namespace MagedIn\Lab;

use MagedIn\Lab\Console\ConsoleBuilder;
use MagedIn\Lab\Console\Input\ArgvInput;

class App
{
    /**
     * @var ConsoleBuilder
     */
    private ConsoleBuilder $consoleBuilder;

    /**
     * @var ArgvInput
     */
    private ArgvInput $input;

    public function __construct(
        ConsoleBuilder $consoleBuilder,
        ArgvInput $input
    ) {
        $this->consoleBuilder = $consoleBuilder;
        $this->input = $input;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        $console = $this->consoleBuilder->build();
        $console->run($this->input);
    }
}

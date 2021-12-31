<?php

declare(strict_types=1);

namespace MagedIn\Lab;

use MagedIn\Lab\Console\ConsoleBuilder;
use MagedIn\Lab\Console\Input\ArgvInput;
use MagedIn\Lab\Console\Output\ConsoleOutput;

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

    /**
     * @var ConsoleOutput
     */
    private ConsoleOutput $consoleOutput;

    public function __construct(
        ConsoleBuilder $consoleBuilder,
        ArgvInput $input,
        ConsoleOutput $consoleOutput
    ) {
        $this->consoleBuilder = $consoleBuilder;
        $this->input = $input;
        $this->consoleOutput = $consoleOutput;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        $console = $this->consoleBuilder->build();
        $console->run($this->input, $this->consoleOutput);
    }
}

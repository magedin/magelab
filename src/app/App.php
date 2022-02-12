<?php
/**
 * MagedIn Technology
 *
 * @category  MagedIn MageLab
 * @copyright Copyright (c) 2021 MagedIn Technology.
 *
 * @author    Tiago Sampaio <tiago.sampaio@magedin.com>
 */

declare(strict_types=1);

namespace MagedIn\Lab;

use Exception;
use MagedIn\Lab\Console\ConsoleBuilder;

class App
{
    /**
     * @var ConsoleBuilder
     */
    private ConsoleBuilder $consoleBuilder;

    public function __construct(
        ConsoleBuilder $consoleBuilder
    ) {
        $this->consoleBuilder = $consoleBuilder;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $application = $this->consoleBuilder->build();
        $application->execute();
    }
}

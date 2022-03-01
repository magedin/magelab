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
use MagedIn\Lab\Helper\DockerLab\DirList;
use MagedIn\Lab\Helper\DockerLab\EnvLoader;

class App
{
    /**
     * @var ConsoleBuilder
     */
    private ConsoleBuilder $consoleBuilder;

    /**
     * @var App\Initializer
     */
    private App\Initializer $initializer;

    public function __construct(
        ConsoleBuilder $consoleBuilder,
        App\Initializer $initializer
    ) {
        $this->consoleBuilder = $consoleBuilder;
        $this->initializer = $initializer;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function run()
    {
        try {
            $application = $this->consoleBuilder->build();
            $this->initializer->initialize();
            $application->execute();
        } catch (\Exception $e) {
            echo $e->getMessage() . "\n";
        }
    }
}

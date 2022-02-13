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
use MagedIn\Lab\Helper\DockerLab\EnvLoader;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Style\SymfonyStyle;

class App
{
    /**
     * @var ConsoleBuilder
     */
    private ConsoleBuilder $consoleBuilder;

    private EnvLoader $envLoader;

    public function __construct(
        ConsoleBuilder $consoleBuilder,
        EnvLoader $envLoader
    ) {
        $this->consoleBuilder = $consoleBuilder;
        $this->envLoader = $envLoader;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function run()
    {
        try {
            $application = $this->consoleBuilder->build();
            $this->envLoader->load();
            $application->execute();
        } catch (\Exception $e) {
            echo $e->getMessage() . "\n";
        }
    }
}

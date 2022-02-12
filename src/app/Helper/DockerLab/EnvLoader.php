<?php
/**
 * MagedIn Technology
 *
 * @category  MagedIn MageLab
 * @copyright Copyright (c) 2022 MagedIn Technology.
 *
 * @author    Tiago Sampaio <tiago.sampaio@magedin.com>
 */

declare(strict_types=1);

namespace MagedIn\Lab\Helper\DockerLab;

use Symfony\Component\Dotenv\Dotenv;

class EnvLoader
{
    private Dotenv $dotenv;

    public function __construct(
        Dotenv $dotenv
    ) {
        $this->dotenv = $dotenv;
    }

    /**
     * @return void
     */
    public function load()
    {
        $rootDir = BasePath::getAbsoluteRootDir();
        $this->dotenv->usePutenv(true)->loadEnv($rootDir . '/.env');
    }
}

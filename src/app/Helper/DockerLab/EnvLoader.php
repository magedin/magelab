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
        if (BasePath::isValid()) {
            $enviFile = $this->getFileLocation();
            if (file_exists($enviFile) && is_readable($enviFile)) {
                $this->dotenv->usePutenv(true)->loadEnv($enviFile);
            }
        }
    }

    /**
     * @return string
     */
    public function getFileLocation(): string
    {
        $rootDir = BasePath::getAbsoluteRootDir();
        return $rootDir . DS . '.env';
    }
}

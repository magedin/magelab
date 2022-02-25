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

namespace MagedIn\Lab\Helper\DockerLab\DockerCompose;

class DockerComposeFilenameResolver
{
    /**
     * @var string
     */
    private string $mainDockerComposeFile = 'docker-compose.yml';

    /**
     * @param string $service
     * @return string
     */
    public function resolveDockerComposeServiceFilename(string $service): string
    {
        $service = preg_replace('[â-zA-Z0-9\.\_\-]', '', $service);
        return 'services' . DS . sprintf('docker-compose.%s.yml', $service);
    }

    /**
     * @return string
     */
    public function getDockerComposeCustomFilename(): string
    {
        return $this->resolveDockerComposeServiceFilename('custom');
    }

    /**
     * @return string
     */
    public function getDockerComposeMainFilename(): string
    {
        return $this->mainDockerComposeFile;
    }
}

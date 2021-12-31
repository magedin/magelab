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

namespace MagedIn\Lab\CommandBuilder;

use MagedIn\Lab\CommandBuilder\DockerCompose\DockerComposeFiles;
use MagedIn\Lab\Helper\DockerLab\BasePath;

class DockerCompose
{
    /**
     * @var DockerComposeFiles
     */
    private DockerComposeFiles $dockerComposeFiles;

    public function __construct(
        DockerComposeFiles $dockerComposeFiles
    ) {
        $this->dockerComposeFiles = $dockerComposeFiles;
    }

    /**
     * @var array
     */
    private array $command = [];

    /**
     * @return array
     */
    public function build(): array
    {
        if (!$this->command) {
            $this->buildCommand();
        }
        return $this->command;
    }

    /**
     * @return void
     */
    private function buildCommand(): void
    {
        $rootDir = BasePath::getAbsoluteRootDir();
        $this->command = ['docker-compose'];

        $command = &$this->command;
        array_map(function ($file) use ($rootDir, &$command) {
            $this->command[] = '-f';
            $this->command[] = $rootDir . DIRECTORY_SEPARATOR . $file;
        }, $this->dockerComposeFiles->load());
    }
}

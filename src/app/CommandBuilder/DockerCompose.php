<?php

declare(strict_types=1);

namespace MageLab\CommandBuilder;

use MageLab\CommandBuilder\DockerCompose\DockerComposeFiles;
use MageLab\Config\DockerLab\BasePath;

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

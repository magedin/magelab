<?php

declare(strict_types=1);

namespace MageLab\CommandBuilder;

class DockerComposeExec
{
    /**
     * @var DockerCompose
     */
    private DockerCompose $dockerCompose;

    public function __construct(
        DockerCompose $dockerCompose
    ) {
        $this->dockerCompose = $dockerCompose;
    }

    /**
     * @return array
     */
    public function build(): array
    {
        $command = $this->dockerCompose->build();
        $command[] = 'exec';
        return $command;
    }
}

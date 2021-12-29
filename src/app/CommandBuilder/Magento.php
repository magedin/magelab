<?php

declare(strict_types=1);

namespace MagedIn\Lab\CommandBuilder;

class Magento
{
    /**
     * @var DockerComposeExec
     */
    private DockerComposeExec $dockerComposeExec;

    public function __construct(
        DockerComposeExec $dockerComposeExec
    ) {
        $this->dockerComposeExec = $dockerComposeExec;
    }

    /**
     * @return array
     */
    public function build(): array
    {
        $command = $this->dockerComposeExec->build();
        $command[] = 'php';
        $command[] = 'bin/magento';
        return $command;
    }
}

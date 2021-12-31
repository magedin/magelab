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

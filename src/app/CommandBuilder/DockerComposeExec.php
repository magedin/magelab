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

class DockerComposeExec implements CommandBuilderInterface
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
     * @param array $subcommands
     * @return array
     */
    public function build(array $subcommands = []): array
    {
        $command = $this->dockerCompose->build(['exec']);
        return array_merge($command, $subcommands);
    }
}

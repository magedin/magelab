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

class DockerComposePhpExec implements CommandBuilderInterface
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
     * @param array $subcommands
     * @return array
     */
    public function build(array $subcommands = []): array
    {
        $command = $this->dockerComposeExec->build(['php']);
        return array_merge($command, $subcommands);
    }
}

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

class Magento
{
    /**
     * @var DockerComposePhpExec
     */
    private DockerComposePhpExec $dockerComposePhpExec;

    public function __construct(
        DockerComposePhpExec $dockerComposePhpExec
    ) {
        $this->dockerComposePhpExec = $dockerComposePhpExec;
    }

    /**
     * @return array
     */
    public function build(): array
    {
        $command = $this->dockerComposePhpExec->build();
        $command[] = 'bin/magento';
        return $command;
    }
}

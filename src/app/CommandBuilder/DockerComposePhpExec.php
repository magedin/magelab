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

class DockerComposePhpExec extends DockerComposeExec
{
    /**
     * @param array $subcommands
     * @return array
     */
    public function build(array $subcommands = [], array $options = []): array
    {
        return array_merge(parent::build(['php']), $options, $subcommands);
    }
}

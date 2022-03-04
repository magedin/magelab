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

class DockerComposeExec extends DockerCompose
{
    /**
     * @inheritDoc
     */
    public function build(array $subcommands = [], array $options = []): array
    {
        $command = ['exec'];
        $root = $options['root'] ?? false;
        if (true === $root) {
            array_push($command, '-u', 'root');
        }
        unset($options['root']);
        return array_merge(parent::build($command, $options), $subcommands);
    }
}

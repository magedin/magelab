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

class Magento extends DockerComposePhpExec
{
    /**
     * @inheritDoc
     */
    public function build(array $subcommands = [], array $options = []): array
    {
        return array_merge(parent::build(['bin/magento']), $options, $subcommands);
    }
}

<?php
/**
 * MagedIn Technology
 *
 * @category  MagedIn MageLab
 * @copyright Copyright (c) 2022 MagedIn Technology.
 *
 * @author    Tiago Sampaio <tiago.sampaio@magedin.com>
 */

namespace MagedIn\Lab\CommandBuilder;

interface CommandBuilderInterface
{
    /**
     * @param array $subcommands
     * @param array $options
     * @return array
     */
    public function build(array $subcommands = [], array $options = []): array;
}

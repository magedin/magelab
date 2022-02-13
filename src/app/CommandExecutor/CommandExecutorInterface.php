<?php
/**
 * MagedIn Technology
 *
 * @category  MagedIn MageLab
 * @copyright Copyright (c) 2022 MagedIn Technology.
 *
 * @author    Tiago Sampaio <tiago.sampaio@magedin.com>
 */

namespace MagedIn\Lab\CommandExecutor;

interface CommandExecutorInterface
{
    /**
     * @param array $commands
     * @param array $config
     * @return mixed
     */
    public function execute(array $commands = [], array $config = []);
}

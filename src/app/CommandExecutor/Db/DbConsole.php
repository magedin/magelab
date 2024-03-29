<?php
/**
 * MagedIn Technology
 *
 * @category  MagedIn MageLab
 * @copyright Copyright (c) 2022 MagedIn Technology.
 *
 * @author    Tiago Sampaio <tiago.sampaio@magedin.com>
 */

declare(strict_types=1);

namespace MagedIn\Lab\CommandExecutor\Db;

class DbConsole extends DbAbstract
{
    /**
     * @return array
     */
    protected function getCommand(): array
    {
        $command = $this->getBaseCommand();
        $command[] = '-A';
        $command[] = $this->getDefaultDatabase();
        return $command;
    }
}

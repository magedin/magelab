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

namespace MagedIn\Lab\Command;

use MagedIn\Lab\Helper\DockerLab\BasePath;
use Symfony\Component\Console\Command\Command as BaseCommand;

class Command extends BaseCommand
{
    /**
     * Commands that can only run inside a DockerLab project must set this option to true.
     * @var bool
     */
    protected bool $isPrivate = true;

    /**
     * @return bool
     */
    public function isEnabled()
    {
        if ($this->isPrivate && !BasePath::isValid()) {
            return false;
        }
        return parent::isEnabled();
    }

    /**
     * @return bool
     */
    public function isProxyCommand(): bool
    {
        return false;
    }
}
